<?php

namespace App\Controller;

use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="app_account")
     */
    public function profile(): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        };

        return $this->render('account/profile.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    /**
     * @Route("/account/edit", name="app_account_edit")
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        };

        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()&&$form->isValid()){
            $em->flush();

            $this->addFlash('success', 'Account updated successfully');

            return $this->redirectToRoute('app_account');


        }

        return $this->render('account/profile_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
