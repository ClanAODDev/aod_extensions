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
    private $url;

    public $data;

    private $base = "https://aod-tracker.tk/v1/api/";

    /**
     * @return object
     */
    public function getDivisionInfo()
    {
        $cacheFile = trailingslashit(AOD_ROOT) . ('./cache/division_data.data');

        if (file_exists($cacheFile)) {
            $data = unserialize(file_get_contents($cacheFile));
            if ($data['timestamp'] > time() - 10 * 60) {
                $feed = $data['divisions'];
            }
        }

        if (empty($feed)) {

            $this->setURL("divisions/info");

            $feed = $this->request();

            $data = [
                'divisions' => $feed,
                'timestamp' => time()
            ];

            file_put_contents($cacheFile, serialize($data));
        }

        return $feed;
    }

    /**
     * @param $string
     */
    private function setURL($string)
    {
        $this->url = $this->base . $string;
    }

    private function request()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

}