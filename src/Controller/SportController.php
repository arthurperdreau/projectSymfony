<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Sport;
use App\Form\CommentType;
use App\Form\SportType;
use App\Repository\SportRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig;
use Symfony\Config\Framework\RequestConfig;

final class SportController extends AbstractController
{
    #[Route('/sports', name: 'sports')]
    public function index(SportRepository $sportRepository): Response
    {
        $sports=$sportRepository->findAll();
        return $this->render('sport/index.html.twig', [
            'controller_name' => 'SportController',
            'sports' => $sports,
        ]);
    }

    #[Route('/sport/{id}', name: 'show_sport', priority: -1)]
    public function show(Sport $sport,  EntityManagerInterface $manager, Request $request): Response
    {
        $comment= new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setSport($sport);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('show_sport', ['id' => $comment->getId()]);
        }

        return $this->render('sport/show.html.twig',[
            'sport' => $sport,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sport/delete/{id}', name: 'delete_sport')]
    public function delete(Sport $sport, EntityManagerInterface $manager): Response
    {
        if ($sport) {
            $manager->remove($sport);
            $manager->flush();
        }
     return $this->redirectToRoute('sports');
    }

    #[Route('/sport/create', name: 'create_sport')]
    public function create(Request $request, EntityManagerInterface $manager):Response
    {
        $sport = new Sport();
        $form=$this->createForm(SportType::class, $sport);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($sport);
            $manager->flush();
            return $this->redirectToRoute('sports');
        }
        return $this->render('sport/create.html.twig',[
            'form'=>$form->createView(),
        ]);
    }

    #[Route('/sport/edit/{id}', name: 'edit_sport')]
    public function edit(Sport $sport, Request $request, EntityManagerInterface $manager):Response
    {
        $form=$this->createForm(SportType::class, $sport);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            return $this->redirectToRoute('sports');
        }
        return $this->render('sport/update.html.twig',[
            "sport"=>$sport,
            "form"=>$form->createView()]
        );

    }
}
