<?php
/*
Plugin Name: ClanAOD Extensions
Plugin URI:  https://github.com/flashadvocate/ClanAOD_Extensions
Description: Extended functionality for ClanAOD Website WP implementation
Version:     0.1.0
Author:      ClanAOD
Author URI:  https://clanaod.net/
*/

defined('ABSPATH') or die();

define('AOD_ROOT', dirname(__FILE__));
define('AOD_TEMPLATES', AOD_ROOT . '/public/templates/');

require __DIR__ . '/vendor/autoload.php';

new \ClanAOD\Extensions();
