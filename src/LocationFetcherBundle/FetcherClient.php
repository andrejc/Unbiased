<?php

namespace LocationFetcherBundle;

use LocationFetcherBundle\Entity\Location;
use LocationFetcherBundle\Exception\ErrorJsonResponseException;
use LocationFetcherBundle\Exception\InvalidJsonResponseException;

class FetcherClient {

    private $guzzleClient;

    public function __construct($guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    public function fetchLocations() {

        $response = $this->guzzleClient->getCommand('get_locations')->execute();
        $responseData = json_decode($response->getBody(true));

        if(json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonResponseException();
        }

        if($responseData->success === false) {
            throw new ErrorJsonResponseException($responseData->data->message, $responseData->data->code);
        }

        $allLocations = array();

        foreach($responseData->data->locations as $locationData) {
            $location = new Location($locationData->name,
                $locationData->coordinates->lat,
                $locationData->coordinates->long);

            $allLocations[] = $location;
        }

        return $allLocations;
    }
}