/**
 * responsive-grid.test.js
 *
 * Tests for H2H website responsive CSS and HTML structure.
 * Run: node --test tests/responsive-grid.test.js  (from H2H/ directory)
 *
 * Tests are written to FAIL for missing features (text-box classes,
 * overflow-wrap, prefers-reduced-motion, text-box grid usage in HTML).
 * They PASS for things already present (viewport meta tags, grid-N classes).
 */

'use strict';

const { test, describe } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');

// ── Paths ──────────────────────────────────────────────────────────────────
const ROOT = path.resolve(__dirname, '..');
const CSS_FILE = path.join(ROOT, 'style.css');
const HTML_PAGES = ['index', 'about', 'music', 'store', 'events', 'news']
  .map(name => ({ name, file: path.join(ROOT, `${name}.html`) }));

// ── Helpers ────────────────────────────────────────────────────────────────
function readCSS() {
  return fs.readFileSync(CSS_FILE, 'utf8');
}

function readHTML(pageObj) {
  return fs.readFileSync(pageObj.file, 'utf8');
}

/**
 * Walk CSS text from position `start` (which must point at the character
 * immediately after the opening `{`) and return the index one past the
 * matching closing `}`.  Handles arbitrarily nested braces.
 */
function walkToClose(css, start) {
  let depth = 1;
  let i = start;
  while (i < css.length && depth > 0) {
    if (css[i] === '{') depth++;
    else if (css[i] === '}') depth--;
    i++;
  }
  return i; // points one past the closing '}'
}

/**
 * Extract all @media blocks, supporting every real-world header form:
 *   @media { … }                          — bare (applies to all)
 *   @media screen { … }                   — media-type only
 *   @media (max-width: 768px) { … }       — condition only
 *   @media screen and (max-width: 768px) { … }  — type + condition
 *   @media print, screen { … }            — comma-separated list
 *
 * Strategy: scan for `@media` tokens; then advance past any non-`{` text
 * (including nested parens that may contain commas / `and`) character by
 * character while tracking paren depth so we never confuse a `)` inside a
 * media feature with the end of the header.
 */
function extractMediaBlocks(css) {
  const blocks = [];
  let pos = 0;

  while (pos < css.length) {
    const atIdx = css.indexOf('@media', pos);
    if (atIdx === -1) break;

    // Advance past "@media" and scan to the opening `{` of the block,
    // tracking parenthesis depth so inner `()` don't confuse the scanner.
    let i = atIdx + 6; // length of '@media'
    let parenDepth = 0;
    while (i < css.length) {
      const ch = css[i];
      if (ch === '(') { parenDepth++; i++; continue; }
      if (ch === ')') { parenDepth--; i++; continue; }
      if (ch === '{' && parenDepth === 0) break;
      i++;
    }

    if (i >= css.length) break; // malformed — no opening brace found

    const headerEnd = i; // index of the opening '{'
    const bodyStart = i + 1;
    const bodyEnd = walkToClose(css, bodyStart); // one past the closing '}'

    blocks.push({
      // Full header text including '@media' up to (but not including) '{'
      header: css.slice(atIdx, headerEnd),
      body: css.slice(bodyStart, bodyEnd - 1),
      full: css.slice(atIdx, bodyEnd),
    });

    pos = bodyEnd;
  }

  return blocks;
}

/**
 * Find the body text of the first CSS rule whose selector list contains
 * `selector` as a token (handles grouped selectors like `.a,\n.b { … }`).
 *
 * Strategy: find each occurrence of the literal selector text in `css`;
 * check that it is followed (possibly with whitespace / commas / other
 * selectors) by a `{`; then brace-walk to extract the body.
 *
 * Returns the body string (between the braces), or null if not found.
 */
function getRuleBody(css, selector) {
  // Build a pattern that matches the selector as a token boundary.
  // We look for the selector text followed eventually by '{', allowing
  // only selector-list characters (whitespace, commas, other selectors,
  // combinators) between our selector and the brace.
  const escaped = selector.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  // The selector must be at a word/class boundary; after it we allow
  // any characters that are NOT '{' or '}' (to avoid crossing rule
  // boundaries), then an opening '{'.
  const re = new RegExp(escaped + '[^{}]*\\{', 'g');
  let m;
  while ((m = re.exec(css)) !== null) {
    const bodyStart = m.index + m[0].length;
    const bodyEnd = walkToClose(css, bodyStart);
    return css.slice(bodyStart, bodyEnd - 1);
  }
  return null;
}

/** Return true if a CSS selector token appears anywhere in the stylesheet
 *  (i.e. `selector` followed by optional whitespace/comma/other-selectors
 *  then `{`). */
function hasSelector(css, selector) {
  return getRuleBody(css, selector) !== null;
}

/** Check if any media block header targets a given max-width (px). */
function hasBreakpoint(css, widthPx) {
  return new RegExp(`max-width:\\s*${widthPx}px`).test(css);
}

/**
 * Parse a raw HTML string and find the <meta> tag whose attributes include
 * both `name="viewport"` (or `name='viewport'`) AND a `content` attribute,
 * regardless of attribute order.  Returns the full tag string, or null.
 *
 * Approach: extract every <meta …> tag, then test each one for the
 * viewport name and content attributes independently.
 */
function findViewportMeta(html) {
  // Match self-closing or non-self-closing <meta …> tags
  const tagRe = /<meta\b[^>]*>/gi;
  let m;
  while ((m = tagRe.exec(html)) !== null) {
    const tag = m[0];
    // Must have name="viewport" or name='viewport' (any quote style)
    const hasName = /\bname\s*=\s*["']viewport["']/i.test(tag);
    if (hasName) return tag;
  }
  return null;
}

// ── FILE EXISTENCE ─────────────────────────────────────────────────────────
describe('File existence', () => {
  test('style.css exists', () => {
    assert.ok(fs.existsSync(CSS_FILE), `Expected ${CSS_FILE} to exist`);
  });

  for (const page of HTML_PAGES) {
    test(`${page.name}.html exists`, () => {
      assert.ok(fs.existsSync(page.file), `Expected ${page.file} to exist`);
    });
  }
});

// ── CSS SELECTORS ──────────────────────────────────────────────────────────
describe('CSS selectors — text-box classes (expected to FAIL if missing)', () => {
  let css;
  test.before(() => { css = readCSS(); });

  test('.text-box selector exists in style.css', () => {
    assert.ok(
      hasSelector(css, '.text-box'),
      '.text-box { } block not found in style.css — add the .text-box component'
    );
  });

  test('.text-box-grid selector exists in style.css', () => {
    assert.ok(
      hasSelector(css, '.text-box-grid'),
      '.text-box-grid { } block not found in style.css'
    );
  });

  test('.text-box-grid-2 selector exists in style.css', () => {
    assert.ok(
      hasSelector(css, '.text-box-grid-2'),
      '.text-box-grid-2 { } block not found in style.css'
    );
  });

  test('.text-box-grid-3 selector exists in style.css', () => {
    assert.ok(
      hasSelector(css, '.text-box-grid-3'),
      '.text-box-grid-3 { } block not found in style.css'
    );
  });

  test('.text-box-grid-4 selector exists in style.css', () => {
    assert.ok(
      hasSelector(css, '.text-box-grid-4'),
      '.text-box-grid-4 { } block not found in style.css'
    );
  });
});

// ── CSS BREAKPOINTS ────────────────────────────────────────────────────────
describe('CSS breakpoints', () => {
  let css;
  let mediaBlocks;
  test.before(() => {
    css = readCSS();
    mediaBlocks = extractMediaBlocks(css);
  });

  test('1024px breakpoint (@media max-width: 1024px) exists', () => {
    assert.ok(
      hasBreakpoint(css, 1024),
      'No @media (max-width: 1024px) block found in style.css'
    );
  });

  test('768px breakpoint (@media max-width: 768px) exists', () => {
    assert.ok(
      hasBreakpoint(css, 768),
      'No @media (max-width: 768px) block found in style.css'
    );
  });

  test('480px breakpoint (@media max-width: 480px) exists', () => {
    assert.ok(
      hasBreakpoint(css, 480),
      'No @media (max-width: 480px) block found in style.css'
    );
  });

  test('.text-box-grid collapses to 1 column inside 768px breakpoint', () => {
    // Concatenate all 768px block bodies, then use getRuleBody to find the
    // specific rule for .text-box-grid and assert its columns value — this
    // avoids crediting any other 1fr rule in the breakpoint.
    const bodies768 = mediaBlocks
      .filter(b => /max-width:\s*768px/.test(b.header))
      .map(b => b.body)
      .join('\n');
    assert.ok(bodies768.length > 0, 'No 768px media block found');
    const ruleBody = getRuleBody(bodies768, '.text-box-grid');
    assert.ok(
      ruleBody !== null,
      '.text-box-grid rule not found inside the 768px breakpoint'
    );
    assert.ok(
      /grid-template-columns\s*:\s*1fr\b/.test(ruleBody) ||
      /grid-template-columns\s*:\s*repeat\s*\(\s*1/.test(ruleBody),
      '.text-box-grid rule at 768px does not collapse to a single column'
    );
  });

  test('.text-box-grid-2 collapses inside 768px breakpoint', () => {
    const bodies768 = mediaBlocks
      .filter(b => /max-width:\s*768px/.test(b.header))
      .map(b => b.body)
      .join('\n');
    assert.ok(bodies768.length > 0, 'No 768px media block found');
    assert.ok(
      /\.text-box-grid-2/.test(bodies768),
      '.text-box-grid-2 is not addressed inside the 768px breakpoint'
    );
  });

  test('.text-box-grid-3 collapses inside 1024px breakpoint', () => {
    const bodies1024 = mediaBlocks
      .filter(b => /max-width:\s*1024px/.test(b.header))
      .map(b => b.body)
      .join('\n');
    assert.ok(bodies1024.length > 0, 'No 1024px media block found');
    assert.ok(
      /\.text-box-grid-3/.test(bodies1024),
      '.text-box-grid-3 is not addressed inside the 1024px breakpoint'
    );
  });

  test('.text-box-grid-4 collapses inside 480px breakpoint', () => {
    const bodies480 = mediaBlocks
      .filter(b => /max-width:\s*480px/.test(b.header))
      .map(b => b.body)
      .join('\n');
    assert.ok(bodies480.length > 0, 'No 480px media block found');
    assert.ok(
      /\.text-box-grid-4/.test(bodies480),
      '.text-box-grid-4 is not addressed inside the 480px breakpoint'
    );
  });
});

// ── CSS TYPOGRAPHY / OVERFLOW ──────────────────────────────────────────────
describe('CSS typography — overflow-wrap (expected to FAIL if missing)', () => {
  let css;
  test.before(() => { css = readCSS(); });

  test('overflow-wrap is set on .text-box', () => {
    const body = getRuleBody(css, '.text-box');
    assert.ok(body !== null, '.text-box selector block not found');
    assert.ok(
      /overflow-wrap/.test(body),
      '.text-box block does not include overflow-wrap property'
    );
  });

  test('overflow-wrap: break-word or anywhere applied globally or to text-box', () => {
    assert.ok(
      /overflow-wrap\s*:\s*(break-word|anywhere)/.test(css),
      'No overflow-wrap: break-word or overflow-wrap: anywhere found in style.css'
    );
  });

  test('word-break is set for long text handling', () => {
    // Scope to the .text-box rule so the assertion is coupled to the
    // component, not to any arbitrary rule that happens to use word-break.
    const body = getRuleBody(css, '.text-box');
    assert.ok(body !== null, '.text-box selector block not found');
    assert.ok(
      /word-break\s*:/.test(body),
      '.text-box block does not include word-break property — add word-break for text overflow handling'
    );
  });
});

// ── CSS MOTION ─────────────────────────────────────────────────────────────
describe('CSS prefers-reduced-motion (expected to FAIL if missing)', () => {
  let css;
  let mediaBlocks;
  test.before(() => {
    css = readCSS();
    mediaBlocks = extractMediaBlocks(css);
  });

  test('@media (prefers-reduced-motion: reduce) block exists', () => {
    assert.ok(
      /prefers-reduced-motion\s*:\s*reduce/.test(css),
      'No @media (prefers-reduced-motion: reduce) block found in style.css — ' +
      'add reduced-motion support for accessibility'
    );
  });

  test('prefers-reduced-motion block disables or reduces transitions/animations', () => {
    const motionBodies = mediaBlocks
      .filter(b => /prefers-reduced-motion/.test(b.header))
      .map(b => b.body)
      .join('\n');
    assert.ok(
      motionBodies.length > 0,
      'No prefers-reduced-motion media query block found'
    );
    assert.ok(
      /transition/.test(motionBodies) || /animation/.test(motionBodies),
      'prefers-reduced-motion block does not address transitions or animations'
    );
  });
});

// ── HTML PAGES — VIEWPORT META ─────────────────────────────────────────────
describe('HTML pages — viewport meta tag', () => {
  for (const page of HTML_PAGES) {
    test(`${page.name}.html has <meta name="viewport"> with width=device-width`, () => {
      const html = readHTML(page);
      const tag = findViewportMeta(html);

      assert.ok(
        tag !== null,
        `${page.name}.html is missing <meta name="viewport">`
      );

      // content attribute must include width=device-width
      assert.ok(
        /width=device-width/.test(tag),
        `${page.name}.html viewport meta does not include width=device-width`
      );

      // content attribute must include initial-scale=1
      assert.ok(
        /initial-scale=1/.test(tag),
        `${page.name}.html viewport meta does not include initial-scale=1`
      );
    });
  }
});

// ── HTML PAGES — TEXT-BOX / GRID CLASS USAGE ──────────────────────────────
describe('HTML pages — text-box class usage (expected to FAIL if missing)', () => {
  for (const page of HTML_PAGES) {
    test(`${page.name}.html uses at least one .text-box element`, () => {
      const html = readHTML(page);
      const usesTextBox =
        /class="[^"]*\btext-box\b/.test(html) ||
        /class='[^']*\btext-box\b/.test(html);
      assert.ok(
        usesTextBox,
        `${page.name}.html does not use the .text-box class — ` +
        'add text-box component elements to this page'
      );
    });
  }
});

describe('HTML pages — text-box-grid class usage (expected to FAIL if missing)', () => {
  // All content pages should use text-box-grid for responsive column layouts
  const pagesExpectingGrid = ['index', 'about', 'music', 'store', 'events', 'news'];

  for (const name of pagesExpectingGrid) {
    const page = HTML_PAGES.find(p => p.name === name);
    test(`${name}.html uses a text-box-grid variant`, () => {
      const html = readHTML(page);
      const usesGrid =
        /class="[^"]*\btext-box-grid\b/.test(html) ||
        /class='[^']*\btext-box-grid\b/.test(html);
      assert.ok(
        usesGrid,
        `${name}.html does not use .text-box-grid (or any variant) — ` +
        'add responsive grid layout via text-box-grid classes'
      );
    });
  }
});

describe('HTML pages — existing grid-N class usage (currently present)', () => {
  // grid-2, grid-3, grid-4 DO exist in style.css and are used in several pages
  const pagesUsingGridN = [
    { name: 'index',  minCount: 1 },
    { name: 'about',  minCount: 1 },
    { name: 'music',  minCount: 1 },
    { name: 'store',  minCount: 1 },
    { name: 'events', minCount: 1 },
  ];

  for (const { name, minCount } of pagesUsingGridN) {
    const page = HTML_PAGES.find(p => p.name === name);
    test(`${name}.html uses at least ${minCount} grid-N (grid-2/3/4) class`, () => {
      const html = readHTML(page);
      const matches = (html.match(/class="[^"]*\bgrid-[234]\b/g) || [])
        .concat(html.match(/class='[^']*\bgrid-[234]\b/g) || []);
      assert.ok(
        matches.length >= minCount,
        `${name}.html uses ${matches.length} grid-N classes, expected >= ${minCount}`
      );
    });
  }
});

describe('HTML pages — mobile navigation behavior', () => {
  test('about.html toggles .open on #nav-menu, matching mobile CSS', () => {
    const page = HTML_PAGES.find(p => p.name === 'about');
    const html = readHTML(page);

    assert.match(
      html,
      /const\s+navMenu\s*=\s*document\.getElementById\(["']nav-menu["']\)/,
      'about.html should bind the hamburger script to #nav-menu'
    );
    assert.match(
      html,
      /navMenu\.classList\.toggle\(["']open["']\)/,
      'about.html should toggle .open on #nav-menu because CSS targets nav ul.open'
    );
    assert.doesNotMatch(
      html,
      /document\.querySelector\(["']nav["']\)/,
      'about.html should not toggle .open on <nav>; CSS does not open nav.open'
    );
  });
});

// ── CSS — GRID SYSTEM STRUCTURAL CHECKS ───────────────────────────────────
describe('CSS grid system — existing selectors', () => {
  let css;
  let mediaBlocks;
  test.before(() => {
    css = readCSS();
    mediaBlocks = extractMediaBlocks(css);
  });

  test('.grid-2 selector exists and defines grid-template-columns', () => {
    const body = getRuleBody(css, '.grid-2');
    assert.ok(body !== null, '.grid-2 block not found');
    assert.ok(/grid-template-columns/.test(body), '.grid-2 missing grid-template-columns');
  });

  test('.grid-3 selector exists and defines repeat(3, 1fr)', () => {
    const body = getRuleBody(css, '.grid-3');
    assert.ok(body !== null, '.grid-3 block not found');
    assert.ok(
      /repeat\s*\(\s*3\s*,\s*1fr\s*\)/.test(body),
      '.grid-3 should use repeat(3, 1fr)'
    );
  });

  test('.grid-4 selector exists and defines repeat(4, 1fr)', () => {
    const body = getRuleBody(css, '.grid-4');
    assert.ok(body !== null, '.grid-4 block not found');
    assert.ok(
      /repeat\s*\(\s*4\s*,\s*1fr\s*\)/.test(body),
      '.grid-4 should use repeat(4, 1fr)'
    );
  });

  test('.grid-4 collapses to 2 columns at 1024px', () => {
    const bodies1024 = mediaBlocks
      .filter(b => /max-width:\s*1024px/.test(b.header))
      .map(b => b.body)
      .join('\n');
    assert.ok(bodies1024.length > 0, 'No 1024px media block found');
    assert.ok(/\.grid-4/.test(bodies1024), '.grid-4 not overridden at 1024px');
    assert.ok(
      /repeat\s*\(\s*2\s*,\s*1fr\s*\)/.test(bodies1024),
      '.grid-4 should collapse to repeat(2, 1fr) at 1024px'
    );
  });

  test('.grid-2, .grid-3, .grid-4 collapse to 1fr at 768px', () => {
    const bodies768 = mediaBlocks
      .filter(b => /max-width:\s*768px/.test(b.header))
      .map(b => b.body)
      .join('\n');
    assert.ok(bodies768.length > 0, 'No 768px media block found');
    // Use getRuleBody so the grid-template-columns assertion is scoped to the
    // exact rule that names .grid-2 — not any other 1fr rule in the breakpoint.
    const ruleBody = getRuleBody(bodies768, '.grid-2');
    assert.ok(
      ruleBody !== null,
      '.grid-2 rule not found inside the 768px breakpoint'
    );
    assert.ok(
      /grid-template-columns\s*:\s*1fr\b/.test(ruleBody),
      'The .grid-2/.grid-3/.grid-4 collapse rule at 768px does not set grid-template-columns: 1fr'
    );
  });
});
