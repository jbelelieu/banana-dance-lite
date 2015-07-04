<?php namespace App;

/**
 * Global functionality used throughout the application.
 *
 * @author      jbelelieu
 * @date        6/28/15
 * @package     Banana Dance Lite
 * @link        http://www.bananadance.org/
 * @license     GPL-3.0
 * @link        http://www.opensource.org/licenses/gpl-3.0.html
 */

session_start();

if (BD_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

$nameMap = require_once dirname(__FILE__) . '/config/structure_names.php';

/**
 * Build a link that can be used by the program.
 *
 * @param   array   $build
 * @param   string  $page
 *
 * @return  string  A working link.
 */
function buildLink(array $build, $page)
{
    return '?c=' . implode('/', $build) . '&p=' . $page;
}


/**
 * Attempts to match the name of a file to the "clean name" within the name map file.
 * If no mapping is found, it will clean it to the best of its ability and return
 * that "cleaner" version of the page name.
 *
 * @param   string  $page   Name of the page, potentially with category path.
 * @param   string  $lang   Language to use.
 *
 * @return  string  Final formatted name.
 */
function findName($page, $lang = '')
{
    global $nameMap;

    if (empty($lang)) $lang = \App\getLanguage();

    $useMap = $nameMap[$lang];

    if (array_key_exists($page, $useMap)) return $useMap[$page];

    $exp = explode('/', $page);

    $last = array_pop($exp);

    return str_replace('_', ' ', str_replace('.md', '', $last));
}


/**
 * Determine the language we are using for the wiki.
 *
 * @return  string
 */
function getLanguage()
{
    if (! empty($_SESSION['bd_language'])) return $_SESSION['bd_language'];

    return BD_DEFAULT_LANGUAGE;
}



/**
 * Get the base URL to use on the wiki.
 */
function getBaseUrl()
{
    if (! empty(BD_BASE_URL)) {
        return BD_BASE_URL;
    } else {
        $path = pathinfo($_SERVER['PHP_SELF']);

        if ($path['dirname'] == '/') return '';

        return $path['dirname'];
    }
}



/**
 * Get the time since a date.
 *
 * @param   int     $time   PHP time() value.
 *
 * @return  string
 */
function timeSince($time)
{
    $time = time() - $time;

    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;

        $numberOfUnits = floor($time / $unit);

        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
    }
}