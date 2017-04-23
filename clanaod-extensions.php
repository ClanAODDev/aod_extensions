<?php

/*
 * Plugin Name: ClanAOD Extensions
 * Plugin URI:  https://github.com/flashadvocate/aod_extensions
 * Description: Extended functionality for ClanAOD Website WP implementation
 * Version:     1.0.0
 * Author:      ClanAOD
 * Author URI:  https://clanaod.net/
 */

use ClanAOD\ExtensionsPlugin;

if ( ! defined('ABSPATH')) {
    _e('Unable to determine the path to WordPress root.');
    return;
}

if ( ! file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    $self = basename(__DIR__);
    $hook = is_multisite() ? 'network_admin_notices' : 'admin_notices';
    add_action($hook, function () use ($self) {
        ?>
        <div class="notice notice-error below-h2 update-nag">
            <p>
                <?php echo $self; ?>: Unable to register autoloader.
                Ensure Composer been initialized properly.
            </p>
        </div>
        <?php
    });
    return;
}

/**
 * Register autoloader
 */
require_once dirname(__FILE__) . '/vendor/autoload.php';

/**
 * Get stuff to do stuff
 */
define('AOD_ROOT', dirname(__FILE__));
define('AOD_TEMPLATES', AOD_ROOT . '/public/templates/');

/**
 * Touch our singleton instance
 */
new ExtensionsPlugin();
