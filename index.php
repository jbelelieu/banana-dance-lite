<?php

require "vendor/autoload.php";
require "app/config/config.php";
require "app/global.php";

/**
 *   ____                                  _____
 *  |  _ \                                |  __ \
 *  | |_) | __ _ _ __   __ _ _ __   __ _  | |  | | __ _ _ __   ___ ___
 *  |  _ < / _` | '_ \ / _` | '_ \ / _` | | |  | |/ _` | '_ \ / __/ _ \
 *  | |_) | (_| | | | | (_| | | | | (_| | | |__| | (_| | | | | (_|  __/
 *  |____/ \__,_|_| |_|\__,_|_| |_|\__,_| |_____/ \__,_|_| |_|\___\___|
 *
 * This banana is ready to dance!
 *
 * @author      jbelelieu
 * @date        6/28/15
 * @package     Banana Dance Lite
 * @link        http://www.bananadance.org/
 * @license     GPL-3.0
 * @link        http://www.opensource.org/licenses/gpl-3.0.html
 */

/*
 * Is the user requesting a named route?
 */
$requestedLink = (! empty($_GET['l'])) ? htmlentities($_GET['l']) : '';

if (! empty($requestedLink)) {
    $routes = require "app/config/named_routes.php";

    if (array_key_exists($requestedLink, $routes)) {
        $link = explode('/', $routes[$requestedLink]);

        $page = array_pop($link);

        $category = implode('/', $link);

        header('Location: index.php?p=' . $page . '&c=' . $category);
        exit;
    }
}

/*
 * Standard usage scenarios.
 */
$page = (! empty($_GET['p'])) ? $_GET['p'] : 'index.md';

$category = (! empty($_GET['c'])) ? $_GET['c'] : '';

$lang = (! empty($_GET['lang'])) ? $_GET['lang'] : '';

$banana = new App\Banana($page, $category, $lang);

if (! empty($_GET['q'])) {
    echo $banana->search($_GET['q'])->getOutput();
    exit;
} else {
    echo $banana->wiki()->getOutput();
    exit;
}