<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PublicationRepository $publicationRepository , CategoryRepository $categoryRepository): Response
    {

        $publication = $publicationRepository->findAll();
        $category = $categoryRepository->findAll();

        return $this->render('home/index.html.twig', [
             'publication' => $publication,
             'category'  => $category,
        ]);
    }
}
