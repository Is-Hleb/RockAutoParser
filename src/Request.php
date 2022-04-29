<?php
namespace IsHleb\Parser;

use GuzzleHttp\Client;

abstract class Request {
    public static function getBody(string $url) : string {
        $client = new Client();
        if(!empty($url)) {
            return $client->get($url)->getBody();
        }
        return $client->get('https://www.rockauto.com/')->getBody();
    }

    public static function postBody(array $payload) {
        $client = new Client();
        $responseBody = $client->post('https://www.rockauto.com/catalog/catalogapi.php', [
            'form_params' => [
                'func' => 'navnode_fetch',
                'payload' => json_encode($payload),
                'api_json_request' => '1',
                'sctchecked' => 1,
                'scbeenloaded' => false,
                'curCartGroupID' => ""
            ]
        ])->getBody();

        file_put_contents('response.txt', (string) $responseBody);

        $json = json_decode((string) $responseBody, true);

        return $json['html_fill_sections']['navchildren['. $payload['jsn']['groupindex'] .']'];
    }

}