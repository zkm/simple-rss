# Simple RSS (SimplePie Demo)

This repo is a lightweight wrapper around SimplePie with a browser demo (`index.php`) and two quick tests: a web test (`test.php`) and a CLI script (`cli_test.php`).

## Requirements

- PHP 7.4–8.4 (8.x recommended)
- Network access to the feed URLs you want to test

## Quick start: browser demo

You can use PHP's built-in web server to try the demo without Apache/Nginx.

1) Start the server from the project root:

   ```sh
   php -S 127.0.0.1:8000
   ```

2) Open the demo and enter a feed URL in the input field:

   - http://127.0.0.1:8000/index.php

   Example feeds to try:

   - https://hnrss.org/frontpage
   - https://www.nasa.gov/rss/dyn/breaking_news.rss
   - https://planet.php.net/rss/atom.xml

   If you see errors, they'll be printed at the top of the page.

## Web test page

`test.php` prints memory/time stats and a dump of the `SimplePie` object.

Open it with a `feed` query param:

- http://127.0.0.1:8000/test.php?feed=https://hnrss.org/frontpage

## CLI test

Run the CLI script with a feed URL as the first argument. It prints item titles and the total count.

```sh
php cli_test.php https://hnrss.org/frontpage
```

Expected output example:

```
Some story title
Another story title
...
Total items: 30
```

Exit codes:

- 0: success
- 1: missing URL
- 2: parser init error (check the printed error message)

## CI

- `.github/workflows/ci.yml` runs on pushes and PRs across PHP 8.1–8.4.
  - Lints all PHP files
  - Runs a smoke test (`ci_smoke.php`) that fetches a feed and asserts `> 0` items
  - You can override the feed by setting a repository variable `FEED_URL` (defaults to https://hnrss.org/frontpage).

## GitHub Pages

- `.github/workflows/pages.yml` builds a static snapshot of `index.php` with a default feed and publishes it to GitHub Pages.
  - The workflow runs on pushes to `master` (or manually via "Run workflow").
  - Output is written to `public/index.html` and deployed.
  - You can customize the default feed by editing the curl line in the workflow.

## Notes

- Caching is disabled in the test paths to make iteration faster. For production, enable caching for performance and to be friendly to feed providers.
- If a feed requires HTTPS with strict ciphers or SNI, make sure your PHP/OpenSSL supports it.
- The demo uses the project-local autoloader (`autoloader.php`) and embeds SimplePie from `library/`.

## Troubleshooting

- SSL certificate errors: update CA bundle or configure stream context appropriately.
- Empty list or 0 items: the feed may be empty, or the server blocked the request; try another feed URL or set a custom user agent via `SimplePie::set_useragent()`.
- Timeouts: some feeds are slow; consider raising default timeouts.
