<?php
# src/AppBundle/Controller/User/BudgetController.php

namespace AppBundle\Controller\User;

use AppBundle\Entity\Budget;
use AppBundle\Entity\User;
use AppBundle\Form\BudgetType;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Class BudgetController
 * @package AppBundle\Controller\User
 */
class BudgetController extends Controller
{
    /**
     * @Rest\View(serializerGroups={"budget"})
     * @Rest\Get("/users/{id}/budget")
     * @param Request $request
     * @return \AppBundle\Entity\Budget|static
     */
    public function getBudgetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')
            ->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            return $this->userNotFound();
        }

        return $user->getBudget();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED,
     *     serializerGroups={"budget"})
     * @Rest\Post("/users/{id}/budget")
     * @param Request $request
     * @return Budget|\Symfony\Component\Form\FormInterface|static
     */
    public function postBudgetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')
            ->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            return $this->userNotFound();
        }

        $budget = new Budget();
        $budget->setUser($user);
        $form = $this->createForm(BudgetType::class, $budget);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($budget);
            $em->flush();
            return $budget;
        } else {
            return $form;
        }
    }

    private function userNotFound()
    {
        return View::create(['message' => 'User not found', Response::HTTP_NOT_FOUND]);
    }
}
