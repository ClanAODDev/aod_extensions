<?php

namespace ClanAOD;

use CPT;
use Jasny\Twig\TextExtension;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

/**
 * Class ExtensionsPlugin
 * @package ClanAOD
 */
class ExtensionsPlugin
{

    /**
     * ExtensionsPlugin constructor.
     */
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
    public function bootstrap()
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

        $this->registerPostType();
    }

    /**
     * Register our divisions post type
     */
    public function registerPostType()
    {
        $divisions = new CPT([
            'post_type_name' => 'divisions',
            'singular' => 'Division',
            'plural' => 'Divisions',
            'slug' => 'divisions',
            'has_archive' => true,
        ]);

        $divisions->menu_icon('dashicons-admin-multisite');

        return $divisions;
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

    /**
     * Landing Page section shortcode
     *
     * @param $attr
     * @param $content
     * @param $tag
     */
    public function landingPageCallback($attr, $content, $tag)
    {
        $attr = shortcode_atts([
            'section_title' => '',
            'show_shadow' => false,
            'section_img' => 0,
            'section_bg_color' => 0,
            'centered' => false,
            'section_bg' => 0,
            'section_class' => 'section',
        ], $attr, $tag);

        $attr['section_title'] = urldecode($attr['section_title']);

        $withShadow = ((bool) $attr['show_shadow']) ? 'with-shadow' : null;
        $centerContent = ((bool) $attr['centered']) ? 'section--centered' : null;
        $sectionClasses = "{$attr['section_class']} {$withShadow}";
        $sectionImage = (wp_kses_post(wp_get_attachment_image($attr['section_img'], 'full')));
        $sectionBgColor = ($attr['section_bg_color']) ?: null;
        $sectionBg = (wp_kses_post(wp_get_attachment_image_url($attr['section_bg'],
            'full')));
        $sectionBgStyle = ($sectionBg)
            ? "style='background: url({$sectionBg}) {$sectionBgColor} no-repeat center 0'"
            : null;

        $this->twig()->display('LandingSection.twig', [
            'centerContent' => $centerContent,
            'sectionClasses' => $sectionClasses,
            'sectionBgStyle' => $sectionBgStyle,
            'sectionImage' => $sectionImage,
            'content' => wpautop($content),
            'attr' => $attr
        ]);
    }

    /**
     * Singleton accessor for the twig environment
     *
     * @return Twig_Environment
     */
    private function twig()
    {
        static $twig;

        if ($twig == null) {

            $loader = new Twig_Loader_Filesystem(
                $this->path('/aod_extensions/resources/views')
            );

            $twig = new Twig_Environment($loader, [
                'debug' => WP_DEBUG,
                'charset' => 'utf-8',
                'cache' => false,
                'auto_reload' => true,
                'strict_variables' => true,
                'autoescape' => false,
                'optimizations' => -1,
            ]);

            if (WP_DEBUG) {
                $twig->addExtension(new Twig_Extension_Debug());
            }
            $twig->addExtension(new TextExtension());
            $twig->addFunction(new Twig_SimpleFunction('asset', [$this, 'asset']));
            $twig->addFunction(new Twig_SimpleFunction('wp_create_nonce', 'wp_create_nonce'));
            $twig->addFunction(new Twig_SimpleFunction('settings_fields', 'settings_fields'));
            $twig->addFunction(new Twig_SimpleFunction('do_settings_sections', 'do_settings_sections'));
            $twig->addFunction(new Twig_SimpleFunction('submit_button', 'submit_button'));
            $twig->addFunction(new Twig_SimpleFunction('__', '__'));
        }
        return $twig;
    }

    /**
     * Path relative to the plugin root
     *
     * @param string $path
     *
     * @return string
     */
    public function path($path)
    {
        return sprintf('%s/%s', rtrim(plugin_dir_path(AOD_ROOT), '/'), ltrim($path, '/'));
    }

    public function registerLandingPageSection()
    {
        $arguments = [
            'label' => 'Landing Page Section',
            'listItemImage' => 'dashicons-admin-page',
            'post-type' => ['page'],
            'attrs' => [
                [
                    'label' => 'Section Title',
                    'type' => 'text',
                    'attr' => 'section_title',
                    'encode' => true,
                ],
                [
                    'label' => 'Section Background Color',
                    'type' => 'text',
                    'attr' => 'section_bg_color',
                ],
                [
                    'label' => 'Section Background',
                    'type' => 'attachment',
                    'attr' => 'section_bg',
                    'libraryType' => ['image'],
                    'addButton' => 'Select Background',
                    'frameTitle' => 'Add section background image',
                ],
                [
                    'label' => 'Section Graphic (optional)',
                    'type' => 'attachment',
                    'attr' => 'section_img',
                    'libraryType' => ['image'],
                    'addButton' => 'Select Image',
                    'frameTitle' => 'Add section image',
                ],
                [
                    'label' => 'Center content',
                    'type' => 'checkbox',
                    'attr' => 'centered',
                ],
                [
                    'label' => 'Show shadow',
                    'type' => 'checkbox',
                    'attr' => 'show_shadow',
                ],
                [
                    'label' => 'Section Class (optional)',
                    'type' => 'text',
                    'attr' => 'section_class',
                    'meta' => [
                        'placeholder' => 'CSS Class Name',
                    ],
                ],
            ],
            'inner_content' => [
                'label' => 'Section Content',
            ],
        ];
        shortcode_ui_register_for_shortcode('section', $arguments);
    }

    public function registerHistorySection()
    {
        $arguments = [
            'label' => 'History Content Section',
            'listItemImage' => 'dashicons-admin-page',
            'post-type' => 'page',
            'attrs' => [
                [
                    'label' => 'Date text',
                    'type' => 'text',
                    'attr' => 'date_text',
                ],
                [
                    'label' => 'Section title',
                    'type' => 'text',
                    'attr' => 'section_title',
                ],
            ],
            'inner_content' => [
                'label' => 'Section Content',
            ],
        ];

        shortcode_ui_register_for_shortcode('history-section', $arguments);
    }

    /**
     * History section shortcode callback
     *
     * @param $attr
     * @param $content
     * @param $tag
     */
    public function historySectionCallback($attr, $content, $tag)
    {
        $this->twig()->display('HistorySection.twig', [
            'templateDirectory' => get_template_directory_uri(),
            'content' => wpautop($content),
            'attr' => shortcode_atts([
                'date_text' => '',
                'section_title' => '',
            ], $attr, $tag)
        ]);
    }

    /**
     * Clan announcements section shortcode
     *
     * @param $attrs
     * @return string
     */
    public function clanAnnouncementsCallback($attrs)
    {
        $attrs['limit'] = (isset($attrs['limit'])) ?: 5;

        if (empty($attrs['url'])) {
            return "Path to feed required";
        }

        $this->twig()->display('ClanAnnouncements.twig', [
            'threads' => Helpers::getRssFeed($attrs['url']),
            'attrs' => $attrs
        ]);
    }

    /**
     * Division section shortcode
     *
     * @param $attr
     * @param $content
     * @param $tag
     */
    public function divisionSectionCallback($attr, $content, $tag)
    {
        $this->twig()->display('DivisionSection.twig', [
            'threads' => Helpers::getRssFeed($attrs['url']),
            'sectionLink' => Helpers::anchored($attr['section_title']),
            'content' => wpautop($content),
            'attr' => shortcode_atts([
                'section_title' => '',
            ], $attr, $tag)
        ]);
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

        if (is_array($twitter_data) && isset($twitter_data['divisions'])) {
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

        $this->twig()->display('TwitterFeed.twig', [
            'feed' => $feed,
        ]);
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
        $divisionFields = [
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

        wp_nonce_field('division_settings_data', 'division_settings_nonce');
        $output = '';

        foreach ($divisionFields as $field) {
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

}
