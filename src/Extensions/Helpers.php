<?php

namespace ClanAOD;

use SimpleXMLElement;

class Helpers
{
    /**
     * Returns XML RSS feed object
     *
     * @param $path
     * @return bool|SimpleXMLElement
     */
    public static function getRssFeed($path)
    {
        if ( ! $path) {
            return false;
        }

        return new SimpleXMLElement(file_get_contents($path));
    }

    /**
     * Converts URL to anchor-safe string
     *
     * @param $url
     * @return mixed
     */
    public static function anchored($url)
    {
        return str_replace(' ', '-', strtolower($url));
    }

    public static function getField($prefix, $field, $id)
    {
        return get_post_meta($id, $prefix . $field, true);
    }
}