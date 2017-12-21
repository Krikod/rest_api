<?php
# src/AppBundle/Controller/Place/ThemeController.php

namespace AppBundle\Controller\Place;

use AppBundle\Entity\Place;
use AppBundle\Entity\Theme;
use AppBundle\Form\ThemeType;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations

/**
 * Class ThemeController
 * @package AppBundle\Controller\Place
 */
class ThemeController extends Controller
{
    /**
     * @Rest\View(serializerGroups={"theme"})
     * @Rest\Get("/places/{id}/themes")
     * @param Request $request
     * @return \AppBundle\Entity\Theme[]|\Doctrine\Common\Collections\ArrayCollection|static
     */
    public function getThemesAction(Request $request)
    {
        $place = $this->getDoctrine()
            ->getRepository('AppBundle:Place')->find($request->get('id'));
        /* @var $place Place */

        if (empty($place)) {
            return $this->placeNotFound();
        }

        return $place->getThemes();
    }

//    /**
//     * @Rest\View(serializerGroups={"theme"})
//     * @Rest\Get("/places/{id}/themes/{id}")
//     * @param $id
//     * @param Request $request
//     * @return Theme|null|object|JsonResponse
//     */
//    public function getThemeAction($id, Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $theme = $em->getRepository('AppBundle:Theme')->find($id);
//
//        if (empty($theme)) {
//            return new JsonResponse(['message' => 'Theme not found'], Response::HTTP_NOT_FOUND);
//        }
//
//        return $theme;
//    }

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
        // Dans config.yml, on a activé les message d'exception de HttpException à true
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Place not found');
//        return View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
    }
}
