<?php

namespace AppBundle\Controller;

use AppBundle\Form\PlaceType;
use FOS\RestBundle\Controller\Annotations as Rest; // Alias pour toutes les annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\ViewHandler; // Service "fos_rest.view_handler" qui permet de gérer les réponses.
use FOS\RestBundle\View\View; // Utilisation de la vue de FOSRestBundle
use AppBundle\Entity\Place;


class PlaceController extends Controller
{
    /**
     * @Rest\View()
     * @Rest\Get("/places")
     * @param Request $request
     * @return JsonResponse
     */
    public function getPlacesAction (Request $request)
    {
        $places = $this->getDoctrine()->getRepository(Place::class)->findAll();
        /* @var $places Place[] */
// Vu que maintenant nous n’avons plus à définir le format dans les actions de nos contrôleurs, nous avons même la
// possibilité de renvoyer directement nos objets sans utiliser l’objet View de FOSRestBundle.

        return $places;

        // Avec nos objets actuels (accesseurs en visibilité public), le sérialiseur de Symfony peut les transformer
        // pour nous. Au lieu de passer un tableau formaté par nos soins, nous allons passer directement une liste
        // d’objets au view handler.


//        $formatted = [];
//
//        foreach ($places as $place) {
//            $formatted[] = [
//                'id' => $place->getId(),
//                'name' => $place->getName(),
//                'address' => $place->getAddress(),
//            ];
//        }

//        // Récupération du view handler
//        $viewHandler = $this->get('fos_rest.view_handler');

//        Pour utiliser l’annotation View, il faut que le *** SensioFrameworkExtraBundle *** soit activé. Mais si on a
//        utilisé l’installateur de Symfony pour créer ce projet, c’est déjà le cas.


        // Création d'une vue FOSRestBundle
//        $view = View::create($places);
//        $view->setFormat('json');
//
//        // Gestion de la réponse
//        return $view;


//        return new JsonResponse($formatted);
    }

    /**
     * @Rest\View()
     * @Rest\Get("/places/{id}")
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function getPlaceAction($id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Place::class);
        $place = $repository->find($id);
        /* @var $place \AppBundle\Entity\Place */

        if (empty($place)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        return $place;
//        $formatted = [
//          'id' => $place->getId(),
//          'name' => $place->getName(),
//          'address' => $place->getAddress(),
//        ];
//        return new JsonResponse($formatted);
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/places")
     * @param Request $request
     */
    public function postPlacesAction(Request $request)
    {
        $place = new Place();
        $form = $this->createForm(PlaceType::class, $place);
        $form->submit($request->request->all());
// Methode Submit au lieu de HandleRequest (->contraintes REST):
        // http://symfony.com/doc/current/form/direct_submit.html

        // Validation des données
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($place);
            $em->flush();

            return $place;

        } else {
            // On renvoie le form car le ViewHandler de FOSRestBundle est conçu
            // pour gérer nativement les formulaires invalides.
            return $form;
        }


//        $place->setName($request->get('name'))
//            ->setAddress($request->get('address'));



        //        Sans le body_listener (=false):
        //        'payload' => json_decode($request->getContent(), true)
//        return [
//            'payload' => [
//                $request->get('name'),
//                $request->get('address')
//            ]
//        ];
    }
}