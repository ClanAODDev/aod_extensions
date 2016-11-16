<?php

namespace ClanAOD\Shortcodes;

use TwitterAPIExchange;

class TwitterFeed
{
    private $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

    private $method = 'GET';

    private $fields = [
        'screen_name' => 'officialclanaod',
        'count' => 5,
        'trim_user' => true,
        'exclude_replies' => true,
        'include_rts' => false
    ];

    private $cacheFile = 'twitter_stream.data';

    public function __construct($config)
    {
        $this->config = $config;

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
     * Fetch Twitter API credentials
     *
     * @return array
     */
    private function settings()
    {
        $twitter = $this->config;

        return [
            'oauth_access_token' => $twitter['oauth_access_token'],
            'oauth_access_token_secret' => $twitter['oauth_access_token_secret'],
            'consumer_key' => $twitter['consumer_key'],
            'consumer_secret' => $twitter['consumer_secret']
        ];
    }

    /**
     * Fetch data and build a cache file
     */
    private function fetchData()
    {
        if (file_exists($this->cacheFile)) {
            $data = unserialize(file_get_contents($this->cacheFile));
            if ($data['timestamp'] > time() - 10 * 60) {
                $feed = $data['twitter_result'];
            }
        }

        if (empty($feed)) {

            $twitter = new TwitterAPIExchange($this->settings());

            $feed = json_decode($twitter->setGetfield(http_build_query($this->fields))
                ->buildOauth($this->url, $this->method)
                ->performRequest());

            $data = [
                'twitter_result' => $feed,
                'timestamp' => time()
            ];

            file_put_contents($this->cacheFile, serialize($data));
        }

        return $feed;
    }
}