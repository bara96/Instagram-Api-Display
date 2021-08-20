<?php

namespace InstagramApiModels;

class Request
{
    /**
     * Perform a POST request
     *
     * @param $url
     * @param $params
     * @param bool $json_decode
     * @return mixed
     * @throws InstagramApiException
     */
    public static function post($url, $params, bool $json_decode = true) {
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($params));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        //execute post
        $response = curl_exec($ch);
        if ($response !== false) {
            if($json_decode)
                return json_decode($response);
            else
                return $response;
        }
        else {
            throw new InstagramApiException("Bad Response on request: $url");
        }
    }

    /**
     * Perform a GET request
     *
     * @param $url
     * @param $params
     * @param bool $json_decode
     * @return mixed
     * @throws InstagramApiException
     */
    public static function get($url, $params, bool $json_decode = true) {
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url . "?" . http_build_query($params));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        //execute post
        $response = curl_exec($ch);
        if ($response !== false) {
            if($json_decode)
                return json_decode($response);
            else
                return $response;
        }
        else {
            throw new InstagramApiException("Bad Response on request: $url");
        }
    }
}