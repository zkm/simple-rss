#!/usr/bin/env php
<?php
// Reduce noise from deprecations in PHP 8.x for this test script
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 'stderr');
// Robust includes from the project root
require_once __DIR__ . '/autoloader.php';
require_once __DIR__ . '/idn/idna_convert.class.php';

// Simple, defensive CLI harness for SimplePie
if (!isset($argv[1]) || trim($argv[1]) === '') {
	fwrite(STDERR, "Usage: php cli_test.php <feed_url>\n");
	exit(1);
}

$feedUrl = $argv[1];

$feed = new SimplePie();
$feed->set_feed_url($feedUrl);
$feed->enable_cache(false); // disable cache for quick testing

// Initialize and handle errors
if (!$feed->init()) {
	fwrite(STDERR, "Failed to initialize feed parser.\n");
	if ($feed->error()) {
		fwrite(STDERR, 'Error: ' . $feed->error() . "\n");
	}
	exit(2);
}

$items = $feed->get_items();

foreach ($items as $item) {
	echo $item->get_title() . "\n";
}

fwrite(STDOUT, 'Total items: ' . $feed->get_item_quantity() . "\n");

exit(0);
?>