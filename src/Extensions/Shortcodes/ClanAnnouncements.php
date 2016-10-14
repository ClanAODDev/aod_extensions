<?php
/**
 * Created by PhpStorm.
 * User: dcdeaton
 * Date: 10/14/16
 * Time: 1:52 PM
 */

namespace ClanAOD\Shortcodes;

use ClanAOD\Helpers;

class ClanAnnouncements
{
    public function __construct()
    {
        add_shortcode('show_clan_announcements', [$this, 'callback']);
    }

    public function callback($attrs, $content = null)
    {
        $attrs['limit'] = ($attrs['limit']) ?: 5;

        if (empty($attrs['url'])) {
            return "Path to feed required";
        }

        $threads = Helpers::getRssFeed($attrs['url']);

        require(AOD_TEMPLATES . '/ClanAnnouncementsTemplate.php');
    }
}

