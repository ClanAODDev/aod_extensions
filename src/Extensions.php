<?php

namespace ClanAOD;

/**
 * Class ExtensionsPlugin
 * @package ClanAOD
 */
final class ExtensionsPlugin
{

    private static $instance;

    private $divisionFields = [
        [
            'id' => 'abbreviation',
            'label' => 'Abbreviation',
            'type' => 'text',
        ],
        [
            'id' => 'application_id',
            'label' => 'Forum Application ID',
            'type' => 'text',
        ],
        [
            'id' => 'division_icon',
            'label' => 'Division Icon',
            'type' => 'media',
        ],
        [
            'id' => 'header_image',
            'label' => 'Header Image',
            'type' => 'media',
        ],
    ];

    public function __construct()
    {
        add_action('init', [$this, 'shortcodeUIDetection']);

        add_action('plugins_loaded', function () {
            $this->bootstrap();
        });
    }

    /**
     * Initialize all the components of our plugin
     */
    private function bootstrap()
    {
        if ( ! file_exists(AOD_ROOT . '/config.php')) {
            wp_die(require(AOD_TEMPLATES . '/InvalidConfigTemplate.php'));
        }

        /**
         * Shortcodes registered by this plugin
         */
        add_shortcode('division-section', [$this, 'divisionSectionCallback']);
        add_shortcode('history-section', [$this, 'historySectionCallback']);
        add_shortcode('section', [$this, 'landingPageCallback']);
        add_shortcode('show_clan_announcements', [$this, 'clanAnnouncementsCallback']);
        add_shortcode('twitter-feed', [$this, 'twitterFeedCallback']);

        /**
         * Action hook callbacks
         */
        add_action('plugins_loaded', [$this, 'registerPostType']);
        add_action('login_enqueue_scripts', [$this, 'customLoginPage']);
        add_action('add_meta_boxes', [$this, 'addMetaBoxes']);
        add_action('admin_footer', [$this, 'adminFooter']);
        add_action('save_post', [$this, 'saveMetaPost']);
        add_action('register_shortcode_ui', [$this, 'registerHistorySection']);
        add_action('register_shortcode_ui', [$this, 'registerDivisionSection']);
        add_action('register_shortcode_ui', [$this, 'registerLandingPageSection']);

        /**
         * filter hook callbacks
         */
        add_filter('enter_title_here', [$this, 'changeTitleText']);
        add_filter('login_headerurl', [$this, 'aodLogoUrl']);
        add_filter('login_headertitle', [$this, 'aodSiteTitle']);
    }

    /**
     * Singleton constructor
     *
     * @param string $init
     *
     * @return static
     */
    public static function init($init)
    {
        if (self::$instance != null) {
            throw new \RuntimeException('Unable to bind ClanAOD Extensions Plugin');
        }
        self::$instance = new static($init);
        return self::$instance;
    }

    /**
     * Singleton accessor
     *
     * @return static
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            throw new \RuntimeException('Tried to access uninitialized ClanAOD Extensions Plugin instance');
        }
        return self::$instance;
    }

    /**
     * Return configured query of divisions
     *
     * @return array
     */
    public static function allDivisions()
    {
        $args = [
            'posts_per_page' => -1,
            'post_type' => 'divisions',
            'orderby' => 'title',
            'order' => 'ASC',
        ];

        return get_posts($args);
    }

    public function clanAnnouncementsCallback($attrs, $content = null)
    {
        $attrs['limit'] = ($attrs['limit']) ?: 5;

        if (empty($attrs['url'])) {
            return "Path to feed required";
        }

        $threads = Helpers::getRssFeed($attrs['url']);

        require(AOD_TEMPLATES . '/ClanAnnouncementsTemplate.php');
    }

    public function divisionSectionCallback($attr, $content, $tag)
    {
        $attr = shortcode_atts([
            'section_title' => '',
        ], $attr, $tag);

        require(AOD_TEMPLATES . '/DivisionSectionTemplate.php');
    }

    /**
     * Shortcode callback
     *
     * No attributes or content to worry about, so we
     * don't both with method arguments
     */
    public function twitterFeedCallback()
    {
        $twitter_data = DBCache::get('twitter_data');
        if (is_array($twitter_data)) {
            if ($twitter_data['timestamp'] > time() - 10 * 60) {
                $feed = $twitter_data['divisions'];
            }
        }

        if (empty($feed)) {

            $feed = (new Twitter())->getfeed();

            $data = [
                'twitter_result' => $feed,
                'timestamp' => time(),
            ];

            DBCache::store('twitter_data', $data);
        }

        require(AOD_TEMPLATES . '/TwitterFeedTemplate.php');
    }

    public function registerDivisionSection()
    {
        $arguments = [
            'label' => 'Division Content Section',
            'listItemImage' => 'dashicons-admin-page',
            'post-type' => ['divisions'],
            'attrs' => [
                [
                    'label' => 'Section Title',
                    'type' => 'text',
                    'attr' => 'section_title',
                ],
            ],
            'inner_content' => [
                'label' => 'Section Content',
            ],
        ];

        shortcode_ui_register_for_shortcode('division-section', $arguments);
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

    /**
     * AOD Customizations
     */

    public function customLoginPage()
    { ?>
        <style type="text/css">
            #login h1 a, .login h1 a {
                background-image: url('https://clanaod.net/wp-content/uploads/2016/12/admin-ajax-e1482993879861.png');
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

    public function addMetaBoxes()
    {
        add_meta_box(
            'division-settings',
            'Division Settings',
            [$this, 'addMetaboxCallback'],
            'divisions',
            'side',
            'high'
        );
    }

    public function addMetaboxCallback()
    {
        wp_nonce_field('division_settings_data', 'division_settings_nonce');
        $output = '';

        foreach ($this->divisionFields as $field) {
            $label = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
            $db_value = get_post_meta($post->ID, 'division_settings_' . $field['id'], true);
            switch ($field['type']) {
                case 'media':
                    $input = sprintf(
                        '<input id="%s" name="%s" type="text" value="%s" required> <input class="button rational-metabox-media" id="%s_button" name="%s_button" type="button" value="Upload" />',
                        $field['id'],
                        $field['id'],
                        $db_value,
                        $field['id'],
                        $field['id']
                    );
                    break;
                default:
                    $input = sprintf(
                        '<input id="%s" name="%s" type="%s" value="%s" required>',
                        $field['id'],
                        $field['id'],
                        $field['type'],
                        $db_value
                    );
            }
            $output .= '<p>' . $label . '<br>' . $input . '</p>';
        }
        echo $output;
    }

    public function adminFooter()
    {
        ?>
        <script>
            // https://codestag.com/how-to-use-wordpress-3-5-media-uploader-in-theme-options/
            jQuery(document).ready(function ($) {
                if (typeof wp.media !== 'undefined') {
                    var _custom_media = true,
                        _orig_send_attachment = wp.media.editor.send.attachment;
                    $('.rational-metabox-media').click(function (e) {
                        var send_attachment_bkp = wp.media.editor.send.attachment;
                        var button = $(this);
                        var id = button.attr('id').replace('_button', '');
                        _custom_media = true;
                        wp.media.editor.send.attachment = function (props, attachment) {
                            if (_custom_media) {
                                $("#" + id).val(attachment.url);
                            } else {
                                return _orig_send_attachment.apply(this, [props, attachment]);
                            }
                        };
                        wp.media.editor.open(button);
                        return false;
                    });
                    $('.add_media').on('click', function () {
                        _custom_media = false;
                    });
                }
            });
        </script><?php
    }

    public function saveMetaPost()
    {
        if ( ! isset($_POST['division_settings_nonce'])) {
            return $post_id;
        }

        $nonce = $_POST['division_settings_nonce'];
        if ( ! wp_verify_nonce($nonce, 'division_settings_data')) {
            return $post_id;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        foreach ($this->fields as $field) {
            if (isset($_POST[$field['id']])) {
                switch ($field['type']) {
                    case 'email':
                        $_POST[$field['id']] = sanitize_email($_POST[$field['id']]);
                        break;
                    case 'text':
                        $_POST[$field['id']] = sanitize_text_field($_POST[$field['id']]);
                        break;
                }
                update_post_meta($post_id, 'division_settings_' . $field['id'], $_POST[$field['id']]);
            } else {
                if ($field['type'] === 'checkbox') {
                    update_post_meta($post_id, 'division_settings_' . $field['id'], '0');
                }
            }
        }
    }

    /**
     * Change custom post type title placeholder for divisions
     *
     * @param $title
     * @return string
     */
    public function changeTitleText($title)
    {
        $screen = get_current_screen();

        if ('divisions' == $screen->post_type) {
            $title = 'AOD Division Name';
        }

        return $title;
    }

    /**
     * Register our divisions post type
     */
    private function registerPostType()
    {
        $divisions = new PostType(
            [
                'post_type_name' => 'divisions',
                'singular' => 'Division',
                'plural' => 'Divisions',
                'slug' => 'divisions',
                'has_archive' => true,
            ]
        );

        $divisions->menu_icon('dashicons-admin-multisite');
    }

    private function initShortcodes()
    {
        new Shortcodes\LandingPageSection();
        new Shortcodes\DivisionSection();
        new Shortcodes\ClanAnnouncements();
        new Shortcodes\HistorySection();
        new Shortcodes\TwitterFeed();
    }
}



