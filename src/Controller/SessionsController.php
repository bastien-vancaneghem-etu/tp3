<?php

namespace App\Controller;

use App\Entity\Sessions;
use App\Entity\User;
use App\Form\SessionsType;
use App\Repository\SessionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/sessions')]
class SessionsController extends AbstractController
{
    #[Route('/', name: 'app_sessions_index', methods: ['GET'])]
    public function index(SessionsRepository $sessionsRepository, Security $security): Response
    {
        $user = $security->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('sessions/index.html.twig', [
                'sessions' => $sessionsRepository->findAll(),
                'role' => 'admin'
            ]);
        }

        else {
            // dd($user->getSessions());
            return $this->render('sessions/index.html.twig', [
                'sessions' => $user->getSessions(),
                'role' => 'user'
            ]);
        }
    }

    #[Route('/new', name: 'app_sessions_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = new Sessions();
        $form = $this->createForm(SessionsType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('app_sessions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sessions/new.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sessions_show', methods: ['GET'])]
    public function show(Sessions $session): Response
    {
        return $this->render('sessions/show.html.twig', [
            'session' => $session,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sessions_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sessions $session, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) { 
            return new Response('Vous n\'avez pas les droits pour accéder à cette page', Response::HTTP_FORBIDDEN);
        }

        $form = $this->createForm(SessionsType::class, $session);
        $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $entityManager->flush();

        //     return $this->redirectToRoute('app_sessions_index', [], Response::HTTP_SEE_OTHER);
        // }

        // dd($form);

        return $this->render('sessions/edit.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sessions_delete', methods: ['POST'])]
    public function delete(Request $request, Sessions $session, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$session->getId(), $request->request->get('_token'))) {
            $entityManager->remove($session);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sessions_index', [], Response::HTTP_SEE_OTHER);
    }
}
