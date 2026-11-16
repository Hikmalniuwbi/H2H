<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$currentPage = 'store';
$db = getDbConnection();
$merchList = [];

if ($db) {
    $res = $db->query("SELECT * FROM merchandise ORDER BY merchandise_id ASC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $merchList[] = $row;
        }
    }
}

// Fallback jika database belum memiliki data merchandise
if (empty($merchList)) {
    $merchList = [
        [
            'merchandise_id' => 1,
            'product_name' => 'Official Card Holder',
            'category' => 'accessories',
            'description' => 'Cute collectable card holder for your favorite photocards',
            'price' => 165000,
            'stock' => 50,
            'image' => 'assets/product/card_holder.png'
        ],
        [
            'merchandise_id' => 2,
            'product_name' => 'Character Keychain',
            'category' => 'accessories',
            'description' => 'Official limited edition character keychain',
            'price' => 197000,
            'stock' => 45,
            'image' => 'assets/product/keyring.webp'
        ],
        [
            'merchandise_id' => 3,
            'product_name' => 'Official Lightstick Ver 1',
            'category' => 'lightstick',
            'description' => 'Wireless bluetooth sync for concerts',
            'price' => 1850000,
            'stock' => 20,
            'image' => 'assets/product/light_stick.webp'
        ],
        [
            'merchandise_id' => 4,
            'product_name' => 'Photocard Pack Summer Ed',
            'category' => 'photocard',
            'description' => 'Pack of 5 random signed photocards',
            'price' => 89000,
            'stock' => 100,
            'image' => 'assets/product/photo_card.webp'
        ],
        [
            'merchandise_id' => 5,
            'product_name' => 'Premium Photo Holder Set',
            'category' => 'accessories',
            'description' => 'House of H : Hide & Seek special edition photo holder',
            'price' => 180000,
            'stock' => 30,
            'image' => 'assets/product/photo_holder.png'
        ],
        [
            'merchandise_id' => 6,
            'product_name' => 'Official Photo Pack Collection',
            'category' => 'photocard',
            'description' => 'High-quality portrait sheets and concept photos',
            'price' => 145000,
            'stock' => 60,
            'image' => 'assets/product/photo_pack.png'
        ],
        [
            'merchandise_id' => 7,
            'product_name' => 'Decorative Sticker Set',
            'category' => 'accessories',
            'description' => 'Hearts2Hearts theme stickers to decorate your journals',
            'price' => 55000,
            'stock' => 150,
            'image' => 'assets/product/sticker_set.png'
        ],
        [
            'merchandise_id' => 8,
            'product_name' => '4-Cut Strip Photo Set',
            'category' => 'photocard',
            'description' => 'Exclusive House of H themed photo strips',
            'price' => 75000,
            'stock' => 70,
            'image' => 'assets/product/strip_photo_set.png'
        ],
        [
            'merchandise_id' => 9,
            'product_name' => 'Beauty Styling Kit',
            'category' => 'accessories',
            'description' => 'Includes heart mini mirror, pink comb, and hair stickers',
            'price' => 135000,
            'stock' => 40,
            'image' => 'assets/product/styling_set.png'
        ],
        [
            'merchandise_id' => 10,
            'product_name' => '"Hearts2House" Cozy Sweatshirt',
            'category' => 'apparel',
            'description' => 'Comfortable grey sweatshirt with embroidered typography',
            'price' => 450000,
            'stock' => 25,
            'image' => 'assets/product/sweatshirt.png'
        ],
        [
            'merchandise_id' => 11,
            'product_name' => 'Hide & Seek Trading Card Set',
            'category' => 'photocard',
            'description' => 'Random photo card deck pack with special hidden elements',
            'price' => 95000,
            'stock' => 80,
            'image' => 'assets/product/trading_card_set.png'
        ],
        [
            'merchandise_id' => 12,
            'product_name' => '"First Heartbeat" T-Shirt',
            'category' => 'apparel',
            'description' => 'Premium 24s cotton oversized fit',
            'price' => 225000,
            'stock' => 35,
            'image' => 'assets/product/tshirt.webp'
        ]
    ];
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Official Store — Hearts2Hearts</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <section class="page-banner" style="background-color: var(--primary-blue)">
    <h1>Official Merchandise Store</h1>
    <p>Support Hearts2Hearts with the latest official collection.</p>
  </section>

  <main>
    <section class="section-blue">
      <div class="container">
        <div class="search-bar">
          <input type="text" id="store-search" placeholder="Search Product" />
        </div>

        <div class="category-filter" id="category-filter">
          <a href="#" class="active-cat" data-cat="all">All Products</a>
          <a href="#" data-cat="lightstick">Lightstick</a>
          <a href="#" data-cat="apparel">Apparel</a>
          <a href="#" data-cat="accessories">Accessories</a>
          <a href="#" data-cat="photocard">Photocard</a>
        </div>

        <div id="no-results" style="display:none;text-align:center;padding:60px 20px;color:var(--text-light);">
          <p style="font-size:16px;">No products match your search.</p>
        </div>

        <div class="grid-4" id="product-grid">
          <?php foreach ($merchList as $item):
            $cat = strtolower($item['category'] ?? 'accessories');
            $stock = (int)($item['stock'] ?? 0);
          ?>
            <div class="product-card" data-category="<?= h($cat) ?>">
              <img src="<?= h($item['image']) ?>" alt="<?= h($item['product_name']) ?>" class="product-img" />
              <div class="product-body">
                <h4><?= h($item['product_name']) ?></h4>
                <p class="product-desc"><?= h($item['description'] ?? '') ?></p>
                <p class="product-price"><?= formatRupiah($item['price']) ?></p>

                <?php if ($stock > 0): ?>
                  <a href="checkout.php?type=merchandise&id=<?= h($item['merchandise_id']) ?>" class="btn btn-dark btn-small" style="width: 100%; text-align: center">Buy Now</a>
                <?php else: ?>
                  <button class="btn btn-outline btn-small" disabled style="width: 100%; opacity: 0.6; cursor: not-allowed; text-align: center">Sold Out</button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 40px">
          <a href="#" class="btn btn-outline">Load More Products</a>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script>
    /* ----- Store: Search & Filter ----- */
    const searchInput = document.getElementById("store-search");
    const productGrid = document.getElementById("product-grid");
    const products = productGrid.querySelectorAll(".product-card");
    const noResults = document.getElementById("no-results");
    const catLinks = document.querySelectorAll("#category-filter a");

    let activeCategory = "all";

    function filterProducts() {
      const query = searchInput.value.toLowerCase().trim();
      let visibleCount = 0;

      products.forEach((card) => {
        const title = card.querySelector("h4")?.textContent?.toLowerCase() || "";
        const desc = card.querySelector(".product-desc")?.textContent?.toLowerCase() || "";
        const cat = card.dataset.category || "";
        const matchesSearch = !query || title.includes(query) || desc.includes(query);
        const matchesCat = activeCategory === "all" || cat === activeCategory;

        if (matchesSearch && matchesCat) {
          card.style.display = "";
          visibleCount++;
        } else {
          card.style.display = "none";
        }
      });

      noResults.style.display = visibleCount === 0 ? "block" : "none";
    }

    if (searchInput) {
      searchInput.addEventListener("input", filterProducts);
    }

    if (catLinks) {
      catLinks.forEach((link) => {
        link.addEventListener("click", (e) => {
          e.preventDefault();
          catLinks.forEach((l) => l.classList.remove("active-cat"));
          link.classList.add("active-cat");
          activeCategory = link.dataset.cat;
          filterProducts();
        });
      });
    }
  </script>
</body>
</html>
