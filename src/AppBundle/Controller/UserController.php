<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Rest\Get("/users")
     * @param Request $request
     * @return JsonResponse
     */
    public function getUsersAction(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        /* @var $users User[] */

        $formatted = [];
        foreach ($users as $user) {
            $formatted[] = [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
            ];
        }

        return new JsonResponse($formatted);
    }

    /**
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

        $formatted = [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
        ];

        return new JsonResponse($formatted);
    }
}
