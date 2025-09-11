<?php
// Robust includes from the project root
include_once(__DIR__ . '/autoloader.php');
include_once(__DIR__ . '/idn/idna_convert.class.php');

// Parse it
$feed = new SimplePie();
if (isset($_GET['feed']) && $_GET['feed'] !== '')
{
	if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
	{
		$_GET['feed'] = stripslashes($_GET['feed']);
	}
	$feed->set_feed_url($_GET['feed']);
	$feed->enable_cache(false);
	$starttime = explode(' ', microtime());
	$starttime = $starttime[1] + $starttime[0];
	$feed->init();
	$endtime = explode(' ', microtime());
	$endtime = $endtime[1] + $endtime[0];
	$time = $endtime - $starttime;
}
else
{
	$time = 'null';
}

$feed->handle_content_type();

?>
<!DOCTYPE html>
<title>SimplePie Test</title>
<pre>
<?php

// memory_get_peak_usage() only exists on older PHP builds without memory limit; guard with function_exists
if (function_exists('memory_get_peak_usage'))
{
	var_dump($time, memory_get_usage(), memory_get_peak_usage());
}
// memory_get_usage() may be unavailable; guard with function_exists
else if (function_exists('memory_get_usage'))
{
	var_dump($time, memory_get_usage());
}
else
{
	var_dump($time);
}

// Output buffer
function callable_htmlspecialchars($string)
{
	return htmlspecialchars($string);
}
ob_start('callable_htmlspecialchars');

// Output
print_r($feed);
ob_end_flush();

?>
</pre>