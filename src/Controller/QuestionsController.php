<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\PublicationType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuestionsController extends AbstractController
{
    #[Route('/questions', name: 'app_questions')]
    public function index(CategoryRepository $categoryRepo , Request $request): Response
    {
        $category =  $categoryRepo->findAll();
        $form = $this->createForm(PublicationType::class);
        $form->handleRequest($request);
        return $this->render('questions/index.html.twig', [
            'category' => $category,
            'publicationType' => $form->createView(),
        ]);
    }
}
