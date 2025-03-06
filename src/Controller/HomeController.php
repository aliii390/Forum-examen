<?php
namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Parsedown;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PublicationRepository $publicationRepository, CategoryRepository $categoryRepository): Response
    {
        $publication = $publicationRepository->findAll();
        $category = $categoryRepository->findAll();
        $user = $this->getUser();

        // Initialiser Parsedown
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true); // Sécurise contre les injections XSS

        // Appliquer Parsedown à chaque publication pour convertir la description en HTML
        foreach ($publication as $pub) {
            $pub->formattedDescription = $parsedown->text($pub->getDescription());
        }

        return $this->render('home/index.html.twig', [
            'publication' => $publication,
            'category' => $category,
            'user' => $user, 
        ]);
    }
}
