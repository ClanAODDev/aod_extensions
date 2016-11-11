<?php

namespace ClanAOD;

class Extensions
{
    public function __construct()
    {
        add_action('init', [$this, 'shortcodeUIDetection']);

        $this->init();
    }

    private function init()
    {
        // init our shortcodes
        new Shortcodes\Section();
        new Shortcodes\DivisionSection();
        new Shortcodes\ClanAnnouncements();

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