<?php

namespace ClanAOD;

use SimpleXMLElement;

class Helpers
{
    public static function getRssFeed($path)
    {
        if ( ! $path) {
            return false;
        }

        $xml = new SimpleXMLElement(file_get_contents($path));
        return $xml;
    }
}