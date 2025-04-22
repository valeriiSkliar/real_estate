<?php

namespace app\components;

use Yii;

class HttpRequest
{
    private $url;
    private $data;
    private $headers;

    public function __construct($url, $data = [], $headers = [])
    {
        $this->url = $url;
        $this->data = $data;
        $this->headers = array_merge([
            'Cache-Control: no-cache',
            'Content-Type: application/json',
            'Accept: */*',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
        ], $headers);
    }

    public function get()
    {
        return $this->sendRequest('GET');
    }

    public function post()
    {
        return $this->sendRequest('POST');
    }

    private function sendRequest($method)
    {
        Yii::info("URL: " . $this->url,'payment');
        Yii::info("Data: " . json_encode($this->data),'payment');

        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, TRUE); // Include headers in output
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_VERBOSE, TRUE); // Enable verbose output
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); // Use HTTP/1.1

        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->data));
        }

        if ($method === 'GET' && !empty($this->data)) {
            $this->url .= '?' . http_build_query($this->data);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);

        $response = curl_exec($curl);

        if ($response === false) {
            Yii::info('cURL error: ' . curl_error($curl),'payment');
        } else {
            $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $response_headers = substr($response, 0, $header_size);
            $body = substr($response, $header_size);
            Yii::info('Response Headers: ' . $response_headers,'payment');
            Yii::info('Response Body: ' . $body,'payment');
        }

        curl_close($curl);

        return json_decode($body, true);
    }
}
