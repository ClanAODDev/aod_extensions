<?php

namespace ClanAOD;

use SimpleXMLElement;

/**
 * Class Helpers
 *
 * @package ClanAOD
 */
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

        if ($data = file_get_contents($path)) {
            $simpleXML = new SimpleXMLElement($data);

            if (method_exists($simpleXML, 'item')) {
                return $simpleXML;
            }

            return [];
        }

        return false;
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

    /**
     * @param $prefix
     * @param $field
     * @param $id
     * @return mixed
     */
    public static function getField($prefix, $field, $id)
    {
        return get_post_meta($id, $prefix . $field, true);
    }

    /**
     * @param $string
     * @return string
     */
    public static function twitterize($string)
    {
        return self::uniquify(
            self::urlify($string)
        );
    }

    /**
     * Drop duplicate
     *
     * @param $string
     * @return string
     */
    private function uniquify($string)
    {
        $arr = explode(" ", $string);
        $arr = array_unique($arr);

        return implode(" ", $arr);
    }

    /**
     * @param $string
     * @return mixed
     */
    private function urlify($string)
    {
        $regex = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

        return (preg_match($regex, $string, $url))
            ? preg_replace($regex, "<a href=\"{$url[0]}\" target=\"_blank\">{$url[0]}</a> ", $string)
            : $string;
    }

    /**
     * @param array $divisions
     * @return array
     */
    public static function filterDivisionCounts($divisions)
    {
        if ( ! is_array($divisions)) {
            return [];
        }

        $data = [];

        foreach ($divisions as $division) {
            $data[$division->abbreviation] = $division->members;
        }

        return $data;
    }
}
