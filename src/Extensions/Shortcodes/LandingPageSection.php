<?php

namespace ClanAOD\Shortcodes;

/**
 * Class LandingPageSection
 * @package ClanAOD\Shortcodes
 */
class LandingPageSection
{

    public $fields = [
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
    ];

    public function __construct()
    {
        add_shortcode('section', [$this, 'callback']);

        add_action('register_shortcode_ui', [$this, 'registerWithShortcake']);
    }

    public function callback($attr, $content, $tag)
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

        /**
         * Handle attribute logic
         */
        $withShadow = ((bool) $attr['show_shadow']) ? 'with-shadow' : null;
        $centerContent = ((bool) $attr['centered']) ? 'section--centered' : null;
        $sectionClasses = "{$attr['section_class']} {$withShadow}";
        $sectionImage = (wp_kses_post(wp_get_attachment_image($attr['section_img'], 'full')));
        $sectionBgColor = ($attr['section_bg_color']) ?: null;
        $sectionBg = (wp_kses_post(wp_get_attachment_image_url($attr['section_bg'],
            'full')));

        require(AOD_TEMPLATES . '/LandingPageSectionTemplate.php');
    }

    public function registerWithShortcake()
    {
        $arguments = [
            'label' => 'Landing Page Section',
            'listItemImage' => 'dashicons-admin-page',
            'post-type' => ['page'],
            'attrs' => $this->fields,
            'inner_content' => [
                'label' => 'Section Content',
            ],
        ];

        shortcode_ui_register_for_shortcode('section', $arguments);
    }
}
