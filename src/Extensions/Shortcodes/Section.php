<?php

namespace ClanAOD\Shortcodes;

/**
 * Class Section
 * @package ClanAOD\Shortcodes
 */
class Section
{

    public function __construct()
    {
        add_shortcode('section', [$this, 'callback']);

        add_action('register_shortcode_ui', [$this, 'registerWithShortcake']);
    }

    public $fields = [
        [
            'label' => 'Section Title',
            'type' => 'text',
            'attr' => 'section_title'
        ],
        [
            'label' => 'Section Class (optional)',
            'type' => 'text',
            'attr' => 'section_name',
            'meta' => [
                'placeholder' => 'CSS Class Name'
            ]
        ],
        [
            'label' => 'Section Graphic (optional)',
            'type' => 'attachment',
            'attr' => 'section_img',
            'libraryType' => ['image'],
            'addButton' => 'Select Image',
            'frameTitle' => 'Add section image'
        ],
        [
            'label' => 'Center content',
            'type' => 'checkbox',
            'attr' => 'centered'
        ],
        [
            'label' => 'Show shadow',
            'type' => 'checkbox',
            'attr' => 'show_shadow'
        ],

    ];

    public function callback($attr, $content, $tag)
    {
        $attr = shortcode_atts([
            'section_name' => 'section',
            'section_title' => '',
            'show_shadow' => false,
            'section_img' => 0,
            'centered' => false,
        ], $attr, $tag);

        /**
         * Handle attribute logic
         */
        $withShadow = ((bool) $attr['show_shadow']) ? 'with-shadow' : null;
        $sectionClasses = "{$attr['section_name']} {$withShadow}";
        $centerContent = ((bool) $attr['centered']) ? 'centered' : null;
        $sectionImage = (wp_kses_post(wp_get_attachment_image($attr['section_img'])));

        require(AOD_TEMPLATES . '/SectionTemplate.php');
    }

    public function registerWithShortcake()
    {
        $arguments = [
            'label' => 'Section',
            'listItemImage' => 'dashicons-admin-page',
            'attrs' => $this->fields,
            'inner_content' => [
                'label' => 'Content'
            ]
        ];

        shortcode_ui_register_for_shortcode('section', $arguments);
    }
}