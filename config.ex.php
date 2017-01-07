<?php

defined('ABSPATH') or die();

/**
 * define application specific configurations
 */

return $config = [

    'api' => [

        'tracker' => [
            'oauth_access_token' => '',
        ],

        'twitter' => [

            // API credentials
            'oauth_access_token' => "",
            'oauth_access_token_secret' => "",
            'consumer_key' => "",
            'consumer_secret' => "",

            // feed configuration
            'twitter_config' => [
                'screen_name' => 'officialclanaod',
                'count' => 5,
                'trim_user' => true,
                'exclude_replies' => true,
                'include_rts' => false,
            ],
        ],
    ],
];
