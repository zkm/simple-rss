<?php
// Simple smoke test for CI: fetch a known feed and assert > 0 items
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
require __DIR__ . '/autoloader.php';
require __DIR__ . '/idn/idna_convert.class.php';

$feedUrl = getenv('FEED_URL') ?: 'https://hnrss.org/frontpage';

$feed = new SimplePie();
$feed->set_feed_url($feedUrl);
$feed->enable_cache(false);
if (!$feed->init()) {
    fwrite(STDERR, "Failed to init feed: " . $feed->error() . "\n");
    exit(2);
}
$count = $feed->get_item_quantity();
if ($count <= 0) {
    fwrite(STDERR, "No items parsed from $feedUrl\n");
    exit(3);
}
// Print a tiny summary
fwrite(STDOUT, "Items: $count\n");
