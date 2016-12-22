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

    private function urlify($string)
    {
        $regex = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

        return (preg_match($regex, $string, $url))
            ? preg_replace($regex, "<a href=\"{$url[0]}\" target='_blank'>{$url[0]}</a> ", $string)
            : $string;
    }

    /**
     * Drop duplicate
     * @param $string
     * @return string
     */
    private function uniquify($string)
    {
        $arr = explode(" ", $string);
        $arr = array_unique($arr);

        return implode(" ", $arr);
    }

    public static function twitterize($string)
    {
        return self::uniquify(
            self::urlify($string)
        );
    }
}