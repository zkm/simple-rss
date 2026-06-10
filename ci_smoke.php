<?php
// Simple smoke test for CI: fetch a known feed and assert > 0 items.
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
require __DIR__ . '/autoloader.php';
require __DIR__ . '/idn/idna_convert.class.php';

$primaryFeedUrl = getenv('FEED_URL') ?: 'https://hnrss.org/frontpage';
$fallbackFeedUrl = getenv('FEED_FALLBACK_URL') ?: 'https://feeds.bbci.co.uk/news/world/rss.xml';

$candidateUrls = array_values(array_unique(array_filter(array(
    trim($primaryFeedUrl),
    trim($fallbackFeedUrl),
))));

$maxAttemptsPerMode = 2;
$lastError = 'Unknown error';

foreach ($candidateUrls as $feedUrl) {
    foreach (array(false, true) as $forceFeed) {
        for ($attempt = 1; $attempt <= $maxAttemptsPerMode; $attempt++) {
            $feed = new SimplePie();
            $feed->set_feed_url($feedUrl);
            $feed->enable_cache(false);
            $feed->set_timeout(20);
            $feed->set_useragent('simple-rss-ci/1.0 (+https://github.com/zkm/simple-rss)');

            if ($forceFeed) {
                $feed->force_feed(true);
            }

            if ($feed->init()) {
                $count = $feed->get_item_quantity();
                if ($count > 0) {
                    fwrite(STDOUT, 'Items: ' . $count . ' from ' . $feedUrl . ($forceFeed ? ' (force_feed)' : '') . "\n");
                    exit(0);
                }
                $lastError = 'No items parsed from ' . $feedUrl;
            } else {
                $lastError = $feed->error() ?: ('init() returned false for ' . $feedUrl);
            }
        }
    }
}

fwrite(STDERR, 'Failed to init feed after retries: ' . $lastError . "\n");
exit(2);
