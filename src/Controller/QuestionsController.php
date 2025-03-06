<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuestionsController extends AbstractController
{
    #[Route('/questions', name: 'app_questions')]
    public function index(CategoryRepository $categoryRepo): Response
    {
        $category =  $categoryRepo->findAll();
        return $this->render('questions/index.html.twig', [
            'category' => $category,
        ]);
    }
}
