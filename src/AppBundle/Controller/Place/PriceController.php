<?php

namespace AppBundle\Controller\Place;

use AppBundle\AppBundle;
use AppBundle\Entity\Place;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Form\PriceType;
use AppBundle\Entity\Price;

/**
 * Class PriceController
 * @package AppBundle\Controller\Place
 */
class PriceController extends Controller
{
    /**
     * @Rest\View()
     * @Rest\Get("/places/{id}/prices")
     * @param Request $request
     */
    public function getPricesAction(Request $request)
    {
        $place = $this->getDoctrine()->getRepository('AppBundle:Place')
         ->find($request->get('id')); // // L'identifiant en tant que paramètre n'est plus nécessaire
        /* @var $place ../Place */

        if (empty($place)) {
            return $this->placeNotFound();
        }
        return $place->getPrices();
    }

//    /**
//     * @Rest\View(statusCode=Response::HTTP_CREATED)
//     * @Rest\Post("/places/{id}/prices")
//     * @param Request $request
//     */
//    public function postPricesAction(Request $request)
//    {
//
//    }

    private function placeNotFound()
    {
        return View::create(['message' => 'Place not found', Response::HTTP_NOT_FOUND]);
    }

//    public function getPrices()
//    {
//        /////////////////////////////////
//    }
}
