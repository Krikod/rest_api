<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\ViewHandler; // Service "fos_rest.view_handler" qui permet de gérer les réponses.
use FOS\RestBundle\View\View; // Utilisation de la vue de FOSRestBundle


class UserController extends Controller
{
    /**
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Get("/users")
     * @param Request $request
     * @return User[]|array
     */
    public function getUsersAction(Request $request)
    {
        $users = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();
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
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Get("/users/{id}")
     * @param $id
     * @param Request $request
     * @return User|null|object|JsonResponse
     */
    public function getUserAction($id, Request $request) {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($id);
        /* @var $user User */

        // Gérer une erreur 404
        if (empty($user)) {
            return $this->userNotFound();
//            return new JsonResponse(['message' => 'User not found', Response::HTTP_NOT_FOUND]);
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
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     * @Rest\Post("/users")
     * @param Request $request
     */
    public function postUsersAction(Request $request)
    {
        $user = new User();
//        Suite à validation plainPassword, on ajoute 'validation_groups'
        $form = $this->createForm(UserType::class, $user, ['validation_groups' =>['Default', 'New']]);
        $form->submit($request->request->all());
        // Methode Submit au lieu de HandleRequest (->contraintes REST)

        // Validation des données
        if ($form->isValid()) {
            // Hashage du mot de passe en clair -> grâce aux config mises en place
            $encoder = $this->get('security.password_encoder');

            // le mot de passe en clair est encodé avant la sauvegarde
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);

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
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($request->get('id'));
        /* @var $user User */

        if ($user) {
            $em->remove($user);
            $em->flush();
        }
    }

    /**
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Put("/users/{id}")
     * @param Request $request
     * @return mixed
     */
    public function updateUserAction(Request $request)
    {
        return $this->updateUser($request, true);
    }

    /**
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Patch("/users/{id}")
     * @param Request $request
     * @return mixed
     */
    public function patchUserAction(Request $request)
    {
        return $this->updateUser($request, false);
    }

    /**
     * @param Request $request
     * @param $clearMissing
     * @return User|null|object|\Symfony\Component\Form\FormInterface|static
     */
    private function updateUser(Request $request, $clearMissing)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($request->get('id'));

        if (empty($user)) {
            return $this->userNotFound();
//            return View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
//            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if ($clearMissing) { // Si une mise à jour complète, le mot de passe doit être validé
            $options = ['validation_groups'=>['Default', 'FullUpdate']];
        } else {
            $options = []; // Le groupe de validation par défaut de Symfony est Default
            // (regroupe toutes les contraintes de validation qui ne sont dans aucun groupe.)
            // VOIR -> http://symfony.com/doc/3.3/validation.html
        }
// On ajoute les options
        $form = $this->createForm(UserType::class, $user, $options);
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            // Si l'utilisateur veut changer son mot de passe
            if (!empty($user->getPlainPassword())) {
                $encoder = $this->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($encoded);
            }

            $em = $this->getDoctrine()->getManager();
            $em->merge($user); // todo voir diff merge() et persist(), suite à l'encoding
//            $em->persist($user);
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerGroups={"place"})
     * @Rest\Get("/users/{id}/suggestions")
     * @param Request $request
     * @return array|static
     */
    public function getUserSuggestionsAction(Request $request)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            return $this->userNotFound();
        }

        $suggestions = [];

        $places = $this->getDoctrine()
            ->getRepository('AppBundle:Place')
            ->findAll();

        foreach ($places as $place) {
            if ($user->preferencesMatch($place->getThemes())
                AND $user->budgetMatch($place->getPrices())) {
                $suggestions[] = $place;
            }
        }

        return $suggestions;
    }

//    /**
//     * @Rest\View(statusCode=Response::HTTP_ACCEPTED)
//     * @Rest\Put("/users/{id}")
//     * @param Request $request
//     */
//    public function putUserAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $user = $em->getRepository('AppBundle:User')
//            ->find($request->get('id'));
//        /* @var $user User */
//
//        if(empty($user)) {
//            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
//        }
//        $form = $this->createForm(UserType::class, $user);
//        $form->submit($request->request->all());
//
//        if ($form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->merge($user);
//            $em->flush();
//            return $user;
//        } else {
//            return $form;
//        }
//    }
    private function userNotFound()
    {
        return View::create(['message' => 'User not found', Response::HTTP_NOT_FOUND]);
    }

}
