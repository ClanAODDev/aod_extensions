<?php

namespace ClanAOD\Metaboxes;

class Divisions
{
    private $screens = [
        'divisions',
    ];

    private $fields = [
        [
            'id' => 'abbreviation',
            'label' => 'Abbreviation',
            'type' => 'text',
        ],
        [
            'id' => 'header_image',
            'label' => 'Header Image',
            'type' => 'media'
        ],
        [
            'id' => 'division_icon',
            'label' => 'Division Icon',
            'type' => 'media'
        ],
        [
            'id' => 'application_id',
            'label' => 'Forum Application ID',
            'type' => 'text'
        ],
    ];

    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('admin_footer', [$this, 'admin_footer']);
        add_action('save_post', [$this, 'save_post']);
    }

    public function add_meta_boxes()
    {
        foreach ($this->screens as $screen) {
            add_meta_box(
                'division-settings',
                __('Division Settings', 'rational-metabox'),
                [$this, 'add_meta_box_callback'],
                $screen,
                'side',
                'default'
            );
        }
    }

    public function add_meta_box_callback($post)
    {
        wp_nonce_field('division_settings_data', 'division_settings_nonce');
        $this->generate_fields($post);
    }

    public function admin_footer()
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
                            ;
                        }
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

    public function generate_fields($post)
    {
        $output = '';
        foreach ($this->fields as $field) {
            $label = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
            $db_value = get_post_meta($post->ID, 'division_settings_' . $field['id'], true);
            switch ($field['type']) {
                case 'media':
                    $input = sprintf(
                        '<input id="%s" name="%s" type="text" value="%s"> <input class="button rational-metabox-media" id="%s_button" name="%s_button" type="button" value="Upload" />',
                        $field['id'],
                        $field['id'],
                        $db_value,
                        $field['id'],
                        $field['id']
                    );
                    break;
                default:
                    $input = sprintf(
                        '<input id="%s" name="%s" type="%s" value="%s">',
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

    public function save_post($post_id)
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
}