<?php
// src/Controller/WeatherController.php

namespace App\Controller;

use App\Entity\Api;
use App\Entity\Temperature;
use App\Form\ApiType;
use App\Form\TemperatureType;
use App\Traits\WeatherTrait;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WeatherController extends AbstractController
{
    use WeatherTrait;

    /**
     * @Route("/", name="_index")
     */
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/weather-api", name="_weather_api")
     */
    public function api(Request $request): Response
    {
        $data = [];

        $api = new Api();
        $form = $this->createForm(ApiType::class, $api);

        $form->handleRequest($request);
        $data['request'] = $request;
        $data['form'] = $form;

        if ($form->isSubmitted() && $form->isValid()) {
            $api = $form->getData();

            $this->em->persist($api);
            $this->em->flush();

            $this->addFlash('success', 'Successfully added new API.');
            return $this->redirectToRoute('_index');
        }

        // get all APIs
        $data['apis'] = $this->getApi();

        return $this->renderForm('api.html.twig', $data);
    }

    /**
     * @Route("/weather-temperature", name="_weather_temperature")
     */
    public function location(Request $request): Response
    {
        $data = [];

        $temp = new Temperature();
        $form = $this->createForm(TemperatureType::class, $temp);
        $data['form'] = $form;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $temp = $form->getData();
            $cacheKey = $temp->getCity() . '_' . $temp->getCountry();

            // Let's cache the API call, if user is querying for the same city and country within the hour, then just use the cache
            $cacheAdapter = new FilesystemAdapter();
            $cache = $cacheAdapter->get($cacheKey, function (ItemInterface $item) use ($temp) {

                $data['apis'] = [];
                $item->expiresAfter(3600); //in seconds

                $query = [
                    'time' => date_create_from_format("Y-m-d H:i:s", date("Y-m-d H:i:s")),
                    'city' => $temp->getCity(),
                    'country' => $temp->getCountry(),
                ];

                $apis = $this->getApi($query);

                foreach ($apis as $name => $api) {
                    $data['apis'][$name] = $this->getTemperature($api['url'], $api['data'], $query);
                }

                $data['average'] = $this->getAverage($data['apis']);

                return $data;
            });

            $data = array_merge($data, $cache);
        }

        return $this->renderForm('temperature.html.twig', $data);
    }
}
