<?php
# src/AppBundle/Controller/Place/ThemeController.php

namespace AppBundle\Controller\Place;

use AppBundle\Entity\Place;
use AppBundle\Entity\Theme;
use AppBundle\Form\ThemeType;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations

class ThemeController extends Controller
{
    /**
     * @Rest\View(SerializerGroups={"theme"})
     * @Rest\Get("/places/{id}/themes")
     * @param Request $request
     * @return \AppBundle\Entity\Theme[]|\Doctrine\Common\Collections\ArrayCollection|static
     */
    public function getThemesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $place = $em->getRepository('AppBundle:Place')->find($request->get('id'));
        /* @var $place Place */

        if (empty($place)) {
            return $this->placeNotFound();
        }

        return $place->getThemes();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"theme"})
     * @Rest\Post("/places/{id}/themes")
     * @param Request $request
     * @return Theme|\Symfony\Component\Form\FormInterface|static
     */
    public function postThemesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $place = $em->getRepository('AppBundle:Place')->find($request->get('id'));
        /* @var $place Place */

        if (empty($place)) {
            return $this->placeNotFound();
        }

        $theme = new Theme();
        $theme->setPlace($place);
        $form = $this->createForm(ThemeType::class, $theme);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($theme);
            $em->flush();
            return $theme;
        } else {
            return $form;
        }
    }

    private function placeNotFound()
    {
        return View::create(['message' => 'Place not found', Response::HTTP_NOT_FOUND]);
    }
}