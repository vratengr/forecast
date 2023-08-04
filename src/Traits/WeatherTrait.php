<?php
// src/Traits/WeatherTrait.php

namespace App\Traits;

use App\Entity\Api;
use App\Entity\Temperature;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

trait WeatherTrait
{
    public $em;
    public $logger;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

     /**
     * Returns API data and the URL complete with its params based on supplied country and city
     */
    public function getApi(array $query = [])
    {
        $data = [];
        $city = (isset($query['city'])) ? $query['city'] : '';
        $country = (isset($query['country'])) ? $query['country'] : '';

        $apis = $this->em->getRepository(Api::class)->findAll();

        foreach ($apis as $api) {
            $url = $api->getUrl();

            if ($city) {
                $url = str_replace('{CITY}', $city, $url);
            }

            if ($country) {
                $url = str_replace('{COUNTRY}', $country, $url);
            }

            $data[$api->getName()] = ['data' => $api, 'url' => $url];
        }

        return $data;
    }


    /**
     * Calls the API and parse the data so it returns the temperature and an error message in case of errors
     */
    public function getTemperature(string $url, Api $api, array $query): array
    {
        $temperature = null;
        $error = '';

        $response = $this->callAPI($url);

        //this app will only work with JSON response for now, so if it's of a different type,
        // it's either: there's no response, there's an error or something we cannot parse for this assignment
        $response = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = 'No data found based on your searched values.';
        } else {
            $temperature = $this->parseResponse($response, $api->getDataIndex(), $query);

            if (is_null($temperature)) {
                // if it's null, then we were unable to find the data
                $error = 'Data index not found in response.';
            } else {
                // save the value in the database as part of the requirements, probably for future use
                $tempEntity = new Temperature();
                $tempEntity->setApiId($api->getId());
                $tempEntity->setQueryTime($query['time']);
                $tempEntity->setCountry($query['country']);
                $tempEntity->setCity($query['city']);
                $tempEntity->setValue($temperature);

                $this->em->persist($tempEntity);
                $this->em->flush();
            }
        }

        return ['error' => $error, 'temperature' => $temperature];
    }

    /**
     * Sums up all the temperature from different sources then returns the average
     */
    public function getAverage(array $apiResponses)
    {
        $sum = 0;
        $count = 0;

        foreach ($apiResponses as $api) {
            //temperature can be 0, so we still need to consider them, but if it's null
            //probably we didn't get a value from the API, so don't count it against the average
            if (!is_null($api['temperature'])) {
                $sum += $api['temperature'];
                $count++;
            }
        }

        return ($count) ? ($sum/$count) : null;
    }

    /**
     * Makes the actual call to the API
     */
    public function callAPI(string $url)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $this->logger->info('API Call URL: ' . $url);
        $response = curl_exec($curl);
        $this->logger->info('API Call Response: ' . print_r($response, true));

        curl_close($curl);

        return $response;
    }

    /**
     * Traverse the response array to retrieve the temperature value
     */
    public function parseResponse(array $response, string $dataIndex)
    {
        $temperature = $response;

        $index = explode(',', $dataIndex);
        foreach ($index as $i) {
            // if the array key does not exist, then either the index provided is in the wrong format
            // or there is really no data from the API
            if (array_key_exists($i, $temperature)) {
                $temperature = $temperature[$i];
            } else {
                $temperature = null;
                break;
            }
        }

        return $temperature;
    }

}
