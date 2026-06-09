<?php
// Start counting time for the page load
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

// Include SimplePie using absolute paths for reliability
include_once(__DIR__ . '/autoloader.php');
include_once(__DIR__ . '/idn/idna_convert.class.php');

// Create a new instance of the SimplePie object
$feed = new SimplePie();

//$feed->force_fsockopen(true);

if (isset($_GET['js']))
{
	SimplePie_Misc::output_javascript();
	die();
}

// Make sure that page is getting passed a URL
if (isset($_GET['feed']) && $_GET['feed'] !== '')
{
	// Strip slashes if magic quotes is enabled (which automatically escapes certain characters)
	if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
	{
		$_GET['feed'] = stripslashes($_GET['feed']);
	}

	// Use the URL that was passed to the page in SimplePie
	$feed->set_feed_url($_GET['feed']);
}

// Allow us to change the input encoding from the URL string if we want to. (optional)
if (!empty($_GET['input']))
{
	$feed->set_input_encoding($_GET['input']);
}

// Allow us to choose to not re-order the items by date. (optional)
if (!empty($_GET['orderbydate']) && $_GET['orderbydate'] == 'false')
{
	$feed->enable_order_by_date(false);
}

// Trigger force-feed
if (!empty($_GET['force']) && $_GET['force'] == 'true')
{
	$feed->force_feed(true);
}

// Initialize the whole SimplePie object.  Read the feed, process it, parse it, cache it, and
// all that other good stuff.  The feed's information will not be available to SimplePie before
// this is called.
$success = $feed->init();

// We'll make sure that the right content type and character encoding gets set automatically.
// This function will grab the proper character encoding, as well as set the content type to text/html.
$feed->handle_content_type();

// When we end our PHP block, we want to make sure our DOCTYPE is on the top line to make
// sure that the browser snaps into Standards Mode.
?><!DOCTYPE html>

<html lang="en-US">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>SimplePie: Demo</title>
<style>
	:root {
		--bg: #f4f1ea;
		--surface: #ffffff;
		--surface-soft: #f8f6f2;
		--text: #1f2a33;
		--muted: #5c6770;
		--border: #d9d2c4;
		--accent: #0f766e;
		--accent-soft: #d7f3f0;
	}

	*,
	*::before,
	*::after {
		box-sizing: border-box;
	}

	html, body,
	h1, h2, h3, h4, h5, h6,
	p, ul, ol, li,
	figure, blockquote {
		margin: 0;
		padding: 0;
	}

	body {
		margin: 0;
		padding: 28px 14px 40px;
		font-size: 16px;
		font-family: "Source Serif Pro", "Palatino Linotype", Palatino, "Book Antiqua", serif;
		line-height: 1.7;
		color: var(--text);
		background:
			radial-gradient(circle at 0 0, rgba(15, 118, 110, 0.1), transparent 38%),
			radial-gradient(circle at 100% 100%, rgba(173, 118, 60, 0.12), transparent 34%),
			var(--bg);
	}

	a {
		color: var(--accent);
		text-decoration: none;
	}

	a:hover,
	a:focus {
		text-decoration: underline;
	}

	#site {
		max-width: 980px;
		margin: 0 auto;
	}

	#content {
		display: grid;
		gap: 14px;
	}

	#sp_results {
		display: grid;
		gap: 14px;
		min-width: 0;
	}

	.chunk {
		background: var(--surface);
		border: 1px solid var(--border);
		border-radius: 14px;
		padding: 16px 18px;
		box-shadow: 0 6px 22px rgba(25, 36, 48, 0.08);
		overflow-wrap: anywhere;
		min-width: 0;
	}

	.chunk.focus {
		background: linear-gradient(180deg, var(--surface-soft), #ffffff 42%);
	}

	#sp_form {
		margin: 0;
	}

	#sp_input p {
		display: grid;
		grid-template-columns: minmax(0, 1fr) auto;
		align-items: center;
		gap: 10px;
		margin: 0;
	}

	#feed_input {
		flex: 1;
		min-width: 0;
		padding: 10px 12px;
		font: inherit;
		border-radius: 10px;
		border: 1px solid var(--border);
		background: #fff;
	}

	.button {
		padding: 10px 16px;
		border: 0;
		border-radius: 10px;
		font: inherit;
		font-weight: 700;
		color: #fff;
		background: var(--accent);
		cursor: pointer;
	}

	.button:hover {
		filter: brightness(0.95);
	}

	.header {
		margin: 0 0 8px;
		line-height: 1.3;
	}

	h4 {
		margin: 0 0 10px;
		line-height: 1.35;
	}

	.footnote {
		font-size: 0.9rem;
		color: var(--muted);
	}

	.sp_errors {
		margin-top: 10px;
		padding: 10px 12px;
		border-radius: 10px;
		border: 1px solid #d53f3f;
		background: #fff5f5;
		color: #7f1d1d;
	}

	/* Prevent feed media from breaking layout */
	#sp_results img,
	#sp_results video,
	#sp_results iframe,
	#sp_results embed,
	#sp_results object {
		display: block;
		max-width: 100%;
		height: auto;
	}

	#sp_results .chunk * {
		max-width: 100%;
	}

	#sp_results .chunk [width] {
		max-width: 100% !important;
	}

	#sp_results .chunk pre,
	#sp_results .chunk code,
	#sp_results .chunk samp {
		white-space: pre-wrap;
		word-break: break-word;
	}

	#sp_results .chunk ul,
	#sp_results .chunk ol {
		padding-left: 1.2rem;
	}

	#sp_results table {
		display: block;
		max-width: 100%;
		overflow-x: auto;
	}

	@media (max-width: 720px) {
		body {
			padding: 16px 8px 24px;
		}

		.chunk {
			padding: 12px;
			border-radius: 12px;
		}

		#sp_input p {
			grid-template-columns: 1fr;
		}

		.button {
			width: 100%;
		}

		h3,
		h4 {
			word-break: break-word;
		}
	}
</style>
</head>

<body id="bodydemo">

<div id="site">

	<div id="content">

		<div class="chunk">
			<form action="" method="get" name="sp_form" id="sp_form">
				<div id="sp_input">
					<!-- If a feed has already been passed through the form, then make sure that the URL remains in the form field. -->
									<p><input type="text" name="feed" value="<?php if ($feed->subscribe_url()) echo $feed->subscribe_url(); ?>" class="text" id="feed_input" /><input type="submit" value="Read" class="button" /></p>
				</div>
			</form>


			<?php
			// Check to see if there are more than zero errors (i.e. if there are any errors at all)
			if ($feed->error())
			{
				// If so, start a <div> element with a classname so we can style it.
				echo '<div class="sp_errors">' . "\r\n";

					// ... and display it.
					echo '<p>' . htmlspecialchars($feed->error()) . "</p>\r\n";

				// Close the <div> element we opened.
				echo '</div>' . "\r\n";
			}
			?>

		</div>

		<div id="sp_results">

			<!-- As long as the feed has data to work with... -->
			<?php if ($success): ?>
				<div class="chunk focus" align="center">

					<!-- If the feed has a link back to the site that publishes it (which 99% of them do), link the feed's title to it. -->
					<h3 class="header"><?php if ($feed->get_link()) echo '<a href="' . $feed->get_link() . '">'; echo $feed->get_title(); if ($feed->get_link()) echo '</a>'; ?></h3>

					<!-- If the feed has a description, display it. -->
					<?php echo $feed->get_description(); ?>

				</div>

				<!-- Let's begin looping through each individual news item in the feed. -->
				<?php foreach($feed->get_items() as $item): ?>
					<div class="chunk">

						<!-- If the item has a permalink back to the original post (which 99% of them do), link the item's title to it. -->
						<h4><?php if ($item->get_permalink()) echo '<a href="' . $item->get_permalink() . '">'; echo $item->get_title(); if ($item->get_permalink()) echo '</a>'; ?>&nbsp;<span class="footnote"><?php echo $item->get_date('j M Y, g:i a'); ?></span></h4>

						<!-- Display the item's primary content. -->
						<?php echo $item->get_content(); ?>

						<?php
						// Check for enclosures.  If an item has any, set the first one to the $enclosure variable.
						if ($enclosure = $item->get_enclosure(0))
						{
							// Use the embed() method to embed the enclosure into the page inline.
							echo '<div align="center">';
							echo '<p>' . $enclosure->embed(array(
								'audio' => './for_the_demo/place_audio.png',
								'video' => './for_the_demo/place_video.png',
								'mediaplayer' => './for_the_demo/mediaplayer.swf',
								'altclass' => 'download'
							)) . '</p>';

							if ($enclosure->get_link() && $enclosure->get_type())
							{
								echo '<p class="footnote" align="center">(' . $enclosure->get_type();
								if ($enclosure->get_size())
								{
									echo '; ' . $enclosure->get_size() . ' MB';
								}
								echo ')</p>';
							}
							if ($enclosure->get_thumbnail())
							{
								echo '<div><img src="' . $enclosure->get_thumbnail() . '" alt="" /></div>';
							}
							echo '</div>';
						}
						?>

					</div>

				<!-- Stop looping through each item once we've gone through all of them. -->
				<?php endforeach; ?>

			<!-- From here on, we're no longer using data from the feed. -->
			<?php endif; ?>

		</div>

		<div>
			<!-- Display how fast the page was rendered. -->
			<p class="footnote">Page processed in <?php $mtime = explode(' ', microtime()); echo round($mtime[0] + $mtime[1] - $starttime, 3); ?> seconds.</p>
		</div>

	</div>

</div>

</body>
</html>
