<?php

namespace ClanAOD\Shortcodes;

/**
 * Class HistorySection
 * @package ClanAOD\Shortcodes
 */
class HistorySection
{
    public function __construct()
    {
        add_shortcode('history-section', [$this, 'callback']);

        add_action('register_shortcode_ui', [$this, 'registerWithShortcake']);
    }

    public $fields = [
        [
            'label' => 'Date text',
            'type' => 'text',
            'attr' => 'date_text'
        ],
    ];

    public function callback($attr, $content, $tag)
    {
        $attr = shortcode_atts([
            'date_text' => '',
        ], $attr, $tag);

        require(AOD_TEMPLATES . '/HistorySectionTemplate.php');
    }

    public function registerWithShortcake()
    {
        $arguments = [
            'label' => 'History Content Section',
            'listItemImage' => 'dashicons-admin-page',
            'post-type' => 'page',
            'attrs' => $this->fields,
            'inner_content' => [
                'label' => 'Section Content'
            ]
        ];

        shortcode_ui_register_for_shortcode('history-section', $arguments);
    }
}
