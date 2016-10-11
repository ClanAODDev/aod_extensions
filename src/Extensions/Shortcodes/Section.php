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
            'label' => 'CSS Class Name',
            'type' => 'text',
            'attr' => 'section_name'
        ],
        [
            'label' => 'Center content?',
            'type' => 'checkbox',
            'attr' => 'centered'
        ],
        [
            'label' => 'Section Title',
            'type' => 'text',
            'attr' => 'section_title'
        ],
    ];

    public function callback($attr, $content, $tag)
    {
        $attr = shortcode_atts([
            'section_name' => '',
            'section_title' => '',
            'centered' => false
        ], $attr, $tag);

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