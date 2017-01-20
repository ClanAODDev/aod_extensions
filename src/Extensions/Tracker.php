<?php

namespace ClanAOD;

/**
 * Mechanism for requests to the Tracker API
 *
 * Class Tracker
 * @package ClanAOD
 */
class Tracker
{
    /**
     * @var
     */
    public $data;
    /**
     * @var
     */
    private $url;
    /**
     * @var string
     */
    private $base = "https://aod-tracker.tk/api/v1";

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var array|mixed
     */
    private $config = [];

    /**
     * Tracker constructor
     */
    public function __construct()
    {
        $this->config = require(AOD_ROOT . '/config.php');
    }

    /**
     * @return array|object
     */
    public function getDivisionInfo()
    {
        $division_data = DBCache::get('division_data');
        if (is_array($division_data)) {
            if ($division_data['timestamp'] > time() - 10 * 60) {
                $feed = $division_data['divisions'];
            }
        }

        if (empty($feed)) {

            $feed = $this->setURL("/divisions")->setHeaders([
                'Accept: application/json',
                'Content-type: application/json',
                'Authorization: Bearer ' .
                $this->config['api']['tracker']['oauth_access_token'],
            ])->request();

            $data = [
                'divisions' => $feed,
                'timestamp' => time(),
            ];

            // handle authentication errors gracefully
            if (property_exists($feed, 'error')) {
                return [];
            }

            DBCache::store('division_data', $data);
        }

        return $feed->data;
    }

    /**
     * @return array|mixed|object
     */
    private function request()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    /**
     * @param $headers
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param $string
     * @return $this
     */
    private function setURL($string)
    {
        $this->url = $this->base . $string;

        return $this;
    }

}
