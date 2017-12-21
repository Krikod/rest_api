<?php
# src/AppBundle/Controller/User/PreferenceController.php

namespace AppBundle\Controller\User;

use AppBundle\Entity\User;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Form\PreferenceType;
use AppBundle\Entity\Preference;

/**
 * Class PreferenceController
 * @package AppBundle\Controller\User
 */
class PreferenceController extends Controller
{
    /**
     * @Rest\View(serializerGroups={"preference"})
     * @Rest\Get("/users/{id}/preferences")
     * @param Request $request
     * @return Preference[]|\Doctrine\Common\Collections\ArrayCollection|static
     */
    public function getPreferencesAction(Request $request)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            return $this->userNotFound();
        }

        return $user->getPreferences();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"preference"})
     * @Rest\Post("/users/{id}/preferences")
     * @param Request $request
     * @return Preference|\Symfony\Component\Form\FormInterface|static
     */
    public function postPreferencesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            return $this->userNotFound();
        }

        $preference = new Preference();
        $preference->setUser($user);
        $form = $this->createForm(PreferenceType::class, $preference);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($preference);
            $em->flush();
            return $preference;
        } else {
            return $form;
        }
    }

    private function userNotFound()
    {
        // Dans config.yml, on a activé les message d'exception de HttpException à true
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('User not found');
//        return View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }
}
