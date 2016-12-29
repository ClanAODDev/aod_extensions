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
        if ( ! file_exists(AOD_ROOT . '/config.php')) {
            wp_die(require(AOD_TEMPLATES . '/InvalidConfigTemplate.php'));
        }

        $this->initShortcodes();

        // metaboxes
        new Metaboxes\Divisions();

        // post types
        new PostTypes\Divisions();

        // custom stuff
        add_action('login_enqueue_scripts', [$this, 'customLoginPage']);
        add_filter('login_headerurl', [$this, 'aodLogoUrl']);
        add_filter('login_headertitle', [$this, 'aodSiteTitle']);
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

    private function initShortcodes()
    {
        new Shortcodes\LandingPageSection();
        new Shortcodes\DivisionSection();
        new Shortcodes\ClanAnnouncements();
        new Shortcodes\HistorySection();
        new Shortcodes\TwitterFeed();
    }


    /**
     * AOD Customizations
     */

    public function customLoginPage()
    { ?>
        <style type="text/css">
            #login h1 a, .login h1 a {
                background-image: url('http://wordpress.clanaod.net/wp-content/uploads/2016/12/admin-ajax-e1482993879861.png');
                padding-bottom: 10px;
            }
        </style>
    <?php }

    function aodLogoUrl()
    {
        return home_url();
    }

    function aodSiteTitle()
    {
        return 'ClanAOD.net';
    }
}



