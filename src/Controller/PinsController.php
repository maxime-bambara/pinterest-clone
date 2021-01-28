<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods="GET")
     * @param PinRepository $pinRepository
     * @return Response
     */
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', compact('pins'));
    }

    /**
     * @Route("/pins/create", name="app_pins_create", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserRepository $userRepos
     * @return Response
     * @Security("is_granted('ROLE_USER') && user.isVerified()")
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepos): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $pin = new Pin;
        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();

            $this->addFlash('success','Pin successfully created!');

            return $this->redirectToRoute('app_home');

        }

        return $this->render('pins/create.html.twig', [
            'monformulaire' => $form->createView()
        ]);

    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_show", methods="GET")
     * @param Pin $pin
     * @return Response
     */
    public function show(Pin $pin): Response
    {
        return $this->render('pins/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pins/{id<[0-9]+>}/edit", name="app_pins_edit", methods={"GET", "PUT"})
     * @param Request $request
     * @param Pin $pin
     * @param EntityManagerInterface $em
     * @return Response
     *@Security("is_granted('PIN_EDIT', pin)")
     */
    public function edit (Request $request, Pin $pin, EntityManagerInterface $em) : Response
    {
        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success','Pin successfully updated!');


            return $this->redirectToRoute('app_home');

        }

        return $this->render('pins/edit.html.twig',[
            'pin'=>$pin,
            'monformulaire'=>$form->createView()
        ]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}/delete", name="app_pins_delete", methods="DELETE")
     * @param Request $request
     * @param Pin $pin
     * @param EntityManagerInterface $em
     * @return Response
     *@Security("is_granted('PIN_DELETE', pin)")
     */
    public function delete (Request $request, Pin $pin, EntityManagerInterface $em) : Response
    {
        if ($this->isCsrfTokenValid('pin_deletion' . $pin->getId(), $request->request->get('crsf_token'))){
            $em->remove($pin);
            $em->flush();

            $this->addFlash('info','Pin successfully deleted!');

        }



        return $this->redirectToRoute('app_home');

    }

}
