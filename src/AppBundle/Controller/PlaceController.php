<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\ViewHandler; // Service "fos_rest.view_handler" qui permet de gérer les réponses.
use FOS\RestBundle\View\View; // Utilisation de la vue de FOSRestBundle
use FOS\RestBundle\Controller\Annotations as Rest; // Alias pour toutes les annotations
use AppBundle\Form\PlaceType;
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

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/places/{id}")
     * @param Request $request
     */
    public function removePlaceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $place = $em->getRepository('AppBundle:Place')
            ->find($request->get('id'));
        /* @var $place Place */

        # Delete doit être une action IDEMPOTENTE: produit le même résultat peu importe
        # le nombre de fois qu’elle est exécutée -> évite d'avoir une erreur serveur 500 si une donnée
        #  n'existe pas ou plus.
        if ($place) {
            $em->remove($place);
            $em->flush();
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/places/{id}")
     * @param Request $request
     * @return mixed
     */
    public function updatePlaceAction(Request $request)
    {
        return $this->updatePlace($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/places/{id}")
     * @param Request $request
     * @return mixed
     */
    public function patchPlaceAction(Request $request)
    {
        return $this->updatePlace($request, false);
    }

    /**
     * @param Request $request
     * @param $clearMissing
     * @return mixed
     */
    private function updatePlace(Request $request, $clearMissing)
    {
       $em = $this->getDoctrine()->getManager();
       $place = $em->getRepository('AppBundle:Place')->find($request->get('id'));

       if (empty($place)) {
           return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
       }

       $form = $this->createForm(PlaceType::class, $place);
        // Le paramètre false dit à Symfony de garder les valeurs dans notre
        // entité si l'utilisateur n'en fournit pas une dans sa requête
       $form->submit($request->request->all(), $clearMissing);

       if ($form->isValid()) {
           $em = $this->getDoctrine()->getManager();
           $em->persist($place);
           $em->flush();
           return $place;
       } else {
           return $form;
       }
    }

//
//
//    /**
//     * @Rest\View(statusCode=Response::HTTP_ACCEPTED)
//     * @Rest\Put("/places/{id}")
//     * @param Request $request
//     */
//    public function putPlaceAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $place = $em->getRepository('AppBundle:Place')
//            ->find($request->get('id')); // L'identifiant en tant que paramètre n'est plus nécessaire
//        /* @var $place Place */
//
//        if(empty($place)) {
//            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
//        }
//
//        $form = $this->createForm(PlaceType::class, $place);
//        $form->submit($request->request->all());
//
//        if ($form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            // l'entité vient de la base, donc le merge n'est pas nécessaire.
//            // il est utilisé juste par soucis de clarté
//            $em->merge($place);
//            $em->flush();
//            return $place;
//        } else {
//            return $form;
//        }
//    }
//
//    /**
//     * @Rest\View()
//     * @Rest\Patch("/places/{id}")
//     * @param Request $request
//     */
//    public function patchPlaceAction(Request $request) {
//        $em = $this->getDoctrine()->getManager();
//        $place = $em->getRepository('AppBundle:Place')
//            ->find($request->get('id')); // L'identifiant en tant que paramètre n'est plus nécessaire
//        /* @var $place Place */
//
//        if(empty($place)) {
//            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
//        }
//
//        $form = $this->createForm(PlaceType::class, $place);
//
//        // rajouter un paramètre dans la méthode submit (clearMissing = false)
//        // => Symfony conservera tous les attributs de l’entité Place qui ne sont pas présents
//        // dans le payload de la requête.
//        $form->submit($request->request->all(), false);
//
//        if ($form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            // l'entité vient de la base, donc le merge n'est pas nécessaire.
//            // il est utilisé juste par soucis de clarté
//            $em->merge($place);
//            $em->flush();
//            return $place;
//        } else {
//            return $form;
//        }
//    }
}