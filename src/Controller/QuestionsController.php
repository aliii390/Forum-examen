<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Publication;
use App\Form\PublicationType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuestionsController extends AbstractController
{
    #[Route('/questions', name: 'app_questions')]
    public function index(CategoryRepository $categoryRepo, Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = $categoryRepo->findAll();
        $question = new Publication();
        $form = $this->createForm(PublicationType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question->setUser($this->getUser());
            $entityManager->persist($question);
            $entityManager->flush();

            $this->addFlash('success', 'Question publiée avec succès.');
            return $this->redirectToRoute('app_questions');
        }

        return $this->render('questions/index.html.twig', [
            'category' => $category,
            'publicationType' => $form->createView(),
        ]);
    }
}
