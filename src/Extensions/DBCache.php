<?php

namespace ClanAOD;

/**
 * Class DBCache
 * @package ClanAOD
 */
class DBCache
{
    /**
     * Plugin prefix for options
     */
    const option_prefix = "aod_extensions_";

    /**
     * @param $option
     * @param $data
     */
    static public function store($option, $data)
    {
        $encodedString = wp_json_encode($data);

        if ($encodedString) {
            update_option(self::option_prefix . $option, $data);
        }

    }

    /**
     * @param $option
     * @return array|bool|mixed|object
     */
    static public function get($option)
    {
        $data = get_option(self::option_prefix . $option);

        if ( ! $data) {
            return false;
        }

        return $data;
    }

}