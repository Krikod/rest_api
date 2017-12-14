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
     * @Rest\View(serializerGroups={"price"})
     * @Rest\Get("/places/{id}/prices")
     * @param Request $request
     * @return Price[]|\Doctrine\Common\Collections\ArrayCollection|static
     */
    public function getPricesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $place = $em->getRepository('AppBundle:Place')
            ->find($request->get('id'));
        // L'identifiant en tant que paramètre n'est plus nécessaire
        /* @var $place Place */

        if (empty($place)) {
            return $this->placeNotFound();
        }

        return $place->getPrices();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED,
     *     serializerGroups={"price"})
     * @Rest\Post("/places/{id}/prices")
     * @param Request $request
     * @return Price|\Symfony\Component\Form\FormInterface|static
     */
    public function postPricesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $place = $em->getRepository('AppBundle:Place')
            ->find($request->get('id'));
        /* @var $place Place */

        if(empty($place)) {
            return $this->placeNotFound();
        }

        $price = new Price();
        $price->setPlace($place); // Ici, le lieu est associé au prix
        $form = $this->createForm(PriceType::class, $price);
        // Le paramètre false dit à Symfony de garder les valeurs dans notre
        // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all());

        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($price);
            $em->flush();
            return $price;
        } else {
            return $form;
        }
    }

    private function placeNotFound()
    {
        return View::create(['message' => 'Place not found', Response::HTTP_NOT_FOUND]);
    }
}
