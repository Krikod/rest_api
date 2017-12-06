<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use FOS\RestBundle\View\ViewHandler; // Service "fos_rest.view_handler" qui permet de gérer les réponses.
//use FOS\RestBundle\View\View; // Utilisation de la vue de FOSRestBundle

class UserController extends Controller
{
    /**
     * @Rest\View()
     * @Rest\Get("/users")
     * @param Request $request
     * @return JsonResponse
     */
    public function getUsersAction(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        /* @var $users User[] */

        return $users;
//        $formatted = [];
//        foreach ($users as $user) {
//            $formatted[] = [
//                'id' => $user->getId(),
//                'firstname' => $user->getFirstname(),
//                'lastname' => $user->getLastname(),
//                'email' => $user->getEmail(),
//            ];
//        }
//
//        return new JsonResponse($formatted);
    }

    /**
     * @Rest\View()
     * @Rest\Get("/users/{id}")
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserAction($id, Request $request) {
        $user = $this->getDoctrine()->getRepository(User::class)
            ->find($id);
        /* @var $user User */


        // Gérer une erreur 404
        if (empty($user)) {
            return new JsonResponse(['message' => 'User not found', Response::HTTP_NOT_FOUND]);
        }

        return $user;

//        $formatted = [
//            'id' => $user->getId(),
//            'firstname' => $user->getFirstname(),
//            'lastname' => $user->getLastname(),
//            'email' => $user->getEmail(),
//        ];
//
//        return new JsonResponse($formatted);
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/users")
     * @param Request $request
     */
    public function postUsersAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());
        // Methode Submit au lieu de HandleRequest (->contraintes REST)

        // Validation des données
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $user;

        } else {
            // On renvoie le form car le ViewHandler de FOSRestBundle est conçu
            // pour gérer nativement les formulaires invalides.
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/users/{id}")
     * @param Request $request
     */
    public function removeUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')
            ->find($request->get('id'));
        /* @var $user User */

        if ($user) {
            $em->remove($user);
            $em->flush();
        }
    }
}
