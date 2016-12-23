<?php

namespace ClanAOD\Repositories;

class DivisionRepository
{
    public static function allDivisions()
    {
        $args = [
            'posts_per_page' => -1,
            'post_type' => 'divisions',
            'orderby' => 'title',
            'order' => 'ASC'
        ];

        return get_posts($args);
    }
}