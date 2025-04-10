<?php

namespace App\Controller;

use App\Entity\CompteBloquer;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PublicationRepository $publicationRepository , CategoryRepository $categoryRepository): Response
    {
        $user = $this->getUser();

        // Si aucun utilisateur n'est connecté, on récupère toutes les publications
        if (!$user) {
            $publication = $publicationRepository->findAll();
        } else {
            $publication = $publicationRepository->findePublicationsBloquer($user);
        }


        $category = $categoryRepository->findAll();

        return $this->render('home/index.html.twig', [
             'publication' => $publication,
             'category'  => $category,
        ]);
    }
    #[Route('/block/{id}', name: 'app_block', methods:['GET'])]
    public function bloquer(int $id , EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $otherUser = $entityManager->getRepository(User::class)->find($id);

        // Assuming you have an entity named CompteBloquer
        $compteBloquer = new CompteBloquer;
        $compteBloquer->setUser($user);
        $compteBloquer->setUserBlocked($otherUser);

        $entityManager->persist($compteBloquer);
        $entityManager->flush();

         // Ajout du message flash
    $this->addFlash('saMarche', 'L\'utilisateur a bien été bloqué');

        return $this->redirectToRoute('app_home');
    }
}
