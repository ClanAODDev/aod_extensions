<?php

namespace ClanAOD;

/**
 * Class Twitch
 *
 * @package ClanAOD
 */
class Twitch
{

    /**
     * @var string
     */
    private $streamUrl = "https://api.twitch.tv/kraken/";
    /**
     * @var
     */
    private $clientId;
    /**
     * @var
     */
    private $channel;

    /**
     * @var
     */
    private $content;

    /**
     * @var mixed
     */
    private $config;

    /**
     * Twitch constructor.
     *
     * @param $clientId
     * @param $channel
     */
    public function __construct($channel)
    {
        $this->config = require(AOD_ROOT . '/config.php');

        if (!isset($this->config['api']['twitch'])) {
            return;
        }

        $this->clientId = $this->config['api']['twitch']['client_id'];

        $this->channel = $channel;
    }

    /**
     * @return array|mixed|object
     */
    public function getChannel()
    {
        $this->content = $this->getRequest('/streams/');

        if ( ! property_exists($this->content, 'stream')) {
            $this->content = $this->getRequest('/channels/');
        }

        return $this->content;
    }

    /**
     * @param string $type
     * @return array|mixed|object
     */
    private function getRequest($type = '/channels/')
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->getStreamUrl($type));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());

        $result = curl_exec($ch);

        curl_close($ch);

        return json_decode($result);
    }

    /**
     * @param $type
     * @return string
     */
    private function getStreamUrl($type)
    {
        return $this->streamUrl . $type . $this->channel;
    }

    /**
     * @return array
     */
    private function getHeaders()
    {
        return [
            'Client-ID: ' . $this->clientId
        ];
    }
}