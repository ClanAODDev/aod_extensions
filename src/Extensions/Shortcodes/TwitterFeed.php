<?php

namespace ClanAOD\Shortcodes;

use ClanAOD\DBCache;
use ClanAOD\Twitter;

class TwitterFeed
{

    public function __construct()
    {
        add_shortcode('twitter-feed', [$this, 'callback']);
    }

    /**
     * Shortcode callback
     *
     * No attributes or content to worry about, so we
     * don't both with method arguments
     */
    public function callback()
    {
        $feed = $this->fetchData();

        require(AOD_TEMPLATES . '/TwitterFeedTemplate.php');
    }

    /**
     * Fetch data and build a cache file
     */
    private function fetchData()
    {
        $twitter_data = DBCache::get('twitter_data');
        if (is_array($twitter_data)) {
            if ($twitter_data['timestamp'] > time() - 10 * 60) {
                $feed = $twitter_data['divisions'];
            }
        }

        if (empty($feed)) {

            $feed = (new Twitter())->getfeed();

            $data = [
                'twitter_result' => $feed,
                'timestamp' => time()
            ];

            DBCache::store('twitter_data', $data);
        }

        return $feed;
    }
}