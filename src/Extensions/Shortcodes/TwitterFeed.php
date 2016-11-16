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
        $twitter = new TwitterAPIExchange($this->settings());

        $feed = json_decode($twitter->setGetfield(http_build_query($this->fields))
            ->buildOauth($this->url, $this->method)
            ->performRequest());

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
}