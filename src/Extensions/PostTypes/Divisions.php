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
}