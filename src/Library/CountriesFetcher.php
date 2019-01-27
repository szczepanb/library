<?php

namespace App\Library;

use GuzzleHttp\Client;


trait CountriesFetcher
{
    public function getCountries(): Array
    {
        $client = new Client([
            'base_uri' => 'https://restcountries.eu/rest/v2/',
        ]);

        $response = $client->request('GET', 'all');
        
        $body = $response->getBody();

        if($countries = json_decode($body->getContents()))
        {
            $return = [];
            foreach($countries as $country)
            {
                $return[$country->name] = $country->name;
            }

            return $return;
        }
        else
            return [];
    }
}
