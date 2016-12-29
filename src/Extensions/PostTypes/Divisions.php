<?php

/**
 * Divisions Post Type
 *
 * Registers Post Type and associated taxonomy
 */

namespace ClanAOD\PostTypes;

class Divisions
{
    public function __construct()
    {
        $this->registerPostType();

        add_filter('enter_title_here', [$this, 'changeTitleText']);
    }

    /**
     * Creates our association
     *
     * @return PostType
     */
    public function registerPostType()
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

        return $divisions;
    }

    public function changeTitleText($title)
    {
        $screen = get_current_screen();

        if ('divisions' == $screen->post_type) {
            $title = 'AOD Division Name';
        }

        return $title;
    }
}
