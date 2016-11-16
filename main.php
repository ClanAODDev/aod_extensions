<?php
/*
 * Plugin Name: ClanAOD Extensions
 * Plugin URI:  https://github.com/flashadvocate/aod_extensions
 * Description: Extended functionality for ClanAOD Website WP implementation
 * Version:     0.1.0
 * Author:      ClanAOD
 * Author URI:  https://clanaod.net/
 */

use ClanAOD\Extensions;

defined('ABSPATH') or die();
define('AOD_ROOT', dirname(__FILE__));
define('AOD_TEMPLATES', AOD_ROOT . '/public/templates/');

require __DIR__ . '/vendor/autoload.php';

new Extensions();

