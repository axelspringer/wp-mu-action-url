<?php

// @codingStandardsIgnoreFile

/**
 * Callback function used by ob_start to replace URL in HTML source.
 * Filter which takes a string and searches for the occurrences of host names
 * defined in ORIGIN_URLs and WP_HOME which are used in some html tags which
 * get replaced with STATIC_URL.
 *
 * @param $source
 * @return mixed
 */
function callbackReplaceUrl($source)
{
    //TODO fix the image/thumb src provider. Do not extend this post-html-regex-overall-performance-killer
    //TODO at least this should be the only place for ob_cache replacements
    //TODO find all other replace approaches in the project ()

    // do nothing if ASSE_URL_REPLACEMENT was not defined or is empty
    if (!defined('ASSE_URL_REPLACEMENT') || (defined('ASSE_URL_REPLACEMENT') && !ASSE_URL_REPLACEMENT)) {
        return $source;
    }

    // do nothing if STATIC_URL was not defined or is empty
    if (!defined('STATIC_URL') || STATIC_URL === "") {
        return $source;
    }

    $originHostNames = array_map('trim', explode(',', ORIGIN_URLS));
    $originHostNames[] = WP_HOME;
    $replaceList = [];

    // replace all "/data/uploads<path>" with "<static_host>/data/uploads<path>"
    $replaceList['/(["\'])(\/?data\/uploads[^\\1]*?)\\1/i'] = function ($m) {
        $path = substr($m[2], 0, 1) === '/' ? $m[2] : '/' . $m[2];
        return $m[1] . STATIC_URL . $path . $m[1];
    };

    // replace all <origin_host>/data/uploads<path> with <static_host>/data/uploads<path>
    foreach ($originHostNames as $hostname) {
        $replaceList['/' . preg_quote($hostname, '/') . '(\/data\/uploads[\\w\\.\\/-]+?)/i'] = function ($m) {
            return STATIC_URL . $m[1];
        };
    }

    return preg_replace_callback_array($replaceList, $source);
}

/**
 * Function to use ob_start.
 *
 * @wp-hook wp_head
 */
function bufferStartReplaceUrl()
{
    ob_start("callbackReplaceUrl");
}
add_action('wp_head', 'bufferStartReplaceUrl');
// Also a possible hook
//add_action('template_redirect', 'bufferStartReplaceUrl');

/**
 * Function to use ob_end_flush.
 *
 * @wp-hook wp_footer
 */
function bufferEndReplaceUrl()
{
    ob_end_flush();
}
add_action('wp_footer', 'bufferEndReplaceUrl');
