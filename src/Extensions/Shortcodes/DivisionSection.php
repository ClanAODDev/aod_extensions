<?php

namespace ClanAOD\Shortcodes;

/**
 * Class Section
 * @package ClanAOD\Shortcodes
 */
class DivisionSection
{

    public function __construct()
    {
        add_shortcode('division-section', [$this, 'callback']);

        add_action('register_shortcode_ui', [$this, 'registerWithShortcake']);
    }

    public $fields = [
        [
            'label' => 'Section Title',
            'type' => 'text',
            'attr' => 'section_title'
        ],
    ];

    public function callback($attr, $content, $tag)
    {
        $attr = shortcode_atts([
            'section_title' => '',
        ], $attr, $tag);

        require(AOD_TEMPLATES . '/DivisionSectionTemplate.php');
    }

    public function registerWithShortcake()
    {
        $arguments = [
            'label' => 'Division Content Section',
            'listItemImage' => 'dashicons-admin-page',
            'post-type' => ['divisions'],
            'attrs' => $this->fields,
            'inner_content' => [
                'label' => 'Section Content'
            ]
        ];

        shortcode_ui_register_for_shortcode('division-section', $arguments);
    }
}