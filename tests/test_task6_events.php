<?php
if (!file_exists(__DIR__ . '/../events.php')) {
    fwrite(STDERR, "FAIL: events.php does not exist\n");
    exit(1);
}

$out = [];
$ret = 0;
exec("\"C:\\laragon\\bin\\php\\php-8.1.10-Win32-vs16-x64\\php.exe\" -l \"" . __DIR__ . "/../events.php\"", $out, $ret);
if ($ret !== 0) {
    fwrite(STDERR, "FAIL: Syntax error in events.php\n");
    exit(1);
}

$content = file_get_contents(__DIR__ . '/../events.php');
if (!str_contains($content, 'tiket.com') && !str_contains($content, 'ticket_link')) {
    fwrite(STDERR, "FAIL: events.php missing external ticket.com link\n");
    exit(1);
}

if (!str_contains($content, 'events-table')) {
    fwrite(STDERR, "FAIL: events.php missing events-table class\n");
    exit(1);
}

echo "PASS: Task 6 events tests passed.\n";
exit(0);
