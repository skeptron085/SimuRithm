<?php

/*
|--------------------------------------------------------------------------
| SimuRithm Flask Connector
|--------------------------------------------------------------------------
| Version : Release 1.3
|--------------------------------------------------------------------------
| This file is the ONLY PHP file that communicates
| with the Flask Machine Learning API.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../config/config.php';

class FlaskConnector
{

    /*
    |--------------------------------------------------------------------------
    | POST Request
    |--------------------------------------------------------------------------
    */

    public function post($endpoint, $payload = [])
    {

        $url = FLASK_API . $endpoint;

        $ch = curl_init($url);

        $jsonPayload = json_encode($payload);

        if ($jsonPayload === false) {

            return [

                "success" => false,

                "message" => "Unable to encode request as JSON."

            ];
        }

        curl_setopt_array($ch, [

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_POST => true,

            CURLOPT_HTTPHEADER => [

                "Content-Type: application/json",

                "Accept: application/json"

            ],

            CURLOPT_POSTFIELDS => $jsonPayload,

            /*
            |--------------------------------------------------------------------------
            | Connection Timeout
            |--------------------------------------------------------------------------
            */

            CURLOPT_CONNECTTIMEOUT => 10,

            /*
            |--------------------------------------------------------------------------
            | Maximum Flask Analysis Time
            |--------------------------------------------------------------------------
            | 300 seconds = 5 minutes
            |--------------------------------------------------------------------------
            */

            CURLOPT_TIMEOUT => 300,

            CURLOPT_FOLLOWLOCATION => true

        ]);

        $response = curl_exec($ch);

        /*
        |--------------------------------------------------------------------------
        | cURL Error
        |--------------------------------------------------------------------------
        */

        if ($response === false) {

            $error = curl_error($ch);

            $errno = curl_errno($ch);

            curl_close($ch);

            return [

                "success" => false,

                "message" => "Unable to communicate with Flask API.",

                "curl_error" => $error,

                "curl_errno" => $errno

            ];
        }

        /*
        |--------------------------------------------------------------------------
        | HTTP Status
        |--------------------------------------------------------------------------
        */

        $http = curl_getinfo(

            $ch,

            CURLINFO_HTTP_CODE

        );

        curl_close($ch);

        /*
        |--------------------------------------------------------------------------
        | Decode JSON
        |--------------------------------------------------------------------------
        */

        $json = json_decode(

            $response,

            true

        );

        /*
        |--------------------------------------------------------------------------
        | Invalid JSON
        |--------------------------------------------------------------------------
        */

        if ($json === null && json_last_error() !== JSON_ERROR_NONE) {

            return [

                "success" => false,

                "message" => "Flask returned invalid JSON.",

                "http_code" => $http,

                "raw_response" => $response

            ];
        }

        /*
        |--------------------------------------------------------------------------
        | HTTP Error
        |--------------------------------------------------------------------------
        */

        if ($http < 200 || $http >= 300) {

            return [

                "success" => false,

                "http_code" => $http,

                "response" => $json

            ];
        }

        return $json;
    }


    /*
    |--------------------------------------------------------------------------
    | GET Request
    |--------------------------------------------------------------------------
    */

    public function get($endpoint)
    {

        $url = FLASK_API . $endpoint;

        $ch = curl_init($url);

        curl_setopt_array($ch, [

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_HTTPGET => true,

            CURLOPT_CONNECTTIMEOUT => 5,

            CURLOPT_TIMEOUT => 10

        ]);

        $response = curl_exec($ch);

        if ($response === false) {

            $error = curl_error($ch);

            $errno = curl_errno($ch);

            curl_close($ch);

            return [

                "success" => false,

                "message" => "Unable to communicate with Flask API.",

                "curl_error" => $error,

                "curl_errno" => $errno

            ];
        }

        $http = curl_getinfo(

            $ch,

            CURLINFO_HTTP_CODE

        );

        curl_close($ch);

        $json = json_decode(

            $response,

            true

        );

        if ($json === null && json_last_error() !== JSON_ERROR_NONE) {

            return [

                "success" => false,

                "message" => "Flask returned invalid JSON.",

                "http_code" => $http,

                "raw_response" => $response

            ];
        }

        if ($http < 200 || $http >= 300) {

            return [

                "success" => false,

                "http_code" => $http,

                "response" => $json

            ];
        }

        return $json;
    }


    /*
    |--------------------------------------------------------------------------
    | Health Check
    |--------------------------------------------------------------------------
    */

    public function health()
    {

        return $this->get("/");
    }
}
