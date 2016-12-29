<?php

namespace ClanAOD;

use TwitterAPIExchange;

class Twitter
{
    private $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

    private $method = 'GET';

    public function __construct()
    {
        $this->config = $this->getConfig(
            require(AOD_ROOT . '/config.php')
        );
    }

    /**
     * @param array $config
     * @return array
     */
    private function getConfig(array $config)
    {
        return $config['api']['twitter'];
    }

    public function getfeed()
    {
        $twitter = new TwitterAPIExchange($this->settings());

        return json_decode(
            $twitter->setGetfield(http_build_query($this->config['twitter_config']))
                ->buildOauth($this->url, $this->method)
                ->performRequest()
        );
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
            'consumer_secret' => $twitter['consumer_secret'],
        ];
    }

}
