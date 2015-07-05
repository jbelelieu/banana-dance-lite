<?php

/**
 * Primary configuration file for the program.
 * 
 * @author      j-belelieu
 * @date        6/28/15
 * @package     Banana Dance Lite
 * @link        http://www.bananadance.org/
 * @license     GPL-3.0
 * @link        http://www.opensource.org/licenses/gpl-3.0.html
 */

// Name of your wiki.
define('BD_NAME', 'Documentation');

// Select your company's branding color for use in the header.
define('BD_BRANDING_COLOR', '#111');

// The name of the theme you wish to use.
// The theme folder is found in your /views/ folder.
define('BD_THEME', 'impulse');

// Base URL to your wiki.
// No trailing slash!
define('BD_BASE_URL', '');

// Default language to use throughout the program.
define('BD_DEFAULT_LANGUAGE', 'en');

// Whether to turn on error reporting during debugging.
define('BD_DEBUG', true);

// Use PHP's standard date formatting:
// http://php.net/manual/en/function.date.php
define('BD_DATE_FORMAT', 'Y/m/d g:ia');