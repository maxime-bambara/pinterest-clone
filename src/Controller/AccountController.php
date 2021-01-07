<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

    /**
     * @Route("/account/change-password", name="app_account_change_password")
     */
    public function changePassword(Request $request, EntityManagerInterface $em,
    UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        };

        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted()&&$form->isValid()){
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $form['plainPassword']->getData()));
            $em->flush();

            $this->addFlash('success', 'Password updated successfully');

            return $this->redirectToRoute('app_account');
        };

        return $this->render('account/profile_change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
