<?php

namespace ClanAOD;

class Extensions
{
    public function __construct()
    {
        add_action('init', [$this, 'shortcodeUIDetection']);

        $this->init();
    }

    /**
     * Initialize all the components of our plugin
     */
    private function init()
    {
        $config = require(AOD_ROOT . '/config.php');

        // init our shortcodes
        new Shortcodes\Section();
        new Shortcodes\DivisionSection();
        new Shortcodes\ClanAnnouncements();
        new Shortcodes\TwitterFeed($config['api']['twitter']);

        // metaboxes
        new Metaboxes\Divisions();

        // post types
        new PostTypes\Divisions();
    }

    /**
     * Generate a notice if Shortcake is not enabled
     */
    public function shortcodeUIDetection()
    {
        if ( ! function_exists('shortcode_ui_register_for_shortcode')) {
            add_action('admin_notices', [$this, 'showShortcakeUINotice']);
        }
    }

    public function showShortcakeUINotice()
    {
        if (current_user_can('activate_plugins')) {
            require(AOD_TEMPLATES . '/RequiresShortcakeUI.php');
        }
    }
}