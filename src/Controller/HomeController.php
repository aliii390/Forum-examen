<?php

namespace App\Controller;

use App\Entity\AjoutAmi;
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

    // route pour bloquer un user 
    #[Route('/block/{id}', name: 'app_block', methods:['GET'])]
    public function bloquer(int $id , EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $otherUser = $entityManager->getRepository(User::class)->find($id);

       
        $compteBloquer = new CompteBloquer;
        $compteBloquer->setUser($user);
        $compteBloquer->setUserBlocked($otherUser);

        $entityManager->persist($compteBloquer);
        $entityManager->flush();

         // Ajout du message flash
    $this->addFlash('userBloquer',    $otherUser->getName() . ' a bien été bloqué');

        return $this->redirectToRoute('app_home');
    }


    // route pour ajoutez en ami
    #[Route('/ajout/{id}', name: 'app_home_ajout', methods:['GET'])]
    public function ajoute(int $id , EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $autreUser = $entityManager->getRepository(User::class)->find($id);

       
        $ajoutAmi = new AjoutAmi;
        $ajoutAmi->setUser($user);
        $ajoutAmi->setUserAjoutez($autreUser);

        $entityManager->persist($ajoutAmi);
        $entityManager->flush();

         // Ajout du message flash
    $this->addFlash('ajoutMarche', 'Vous avez bien ajouté ' . $autreUser->getName() . ' en ami');
    // dd($user);

    return $this->redirectToRoute('app_user_profile', [
        'name' => $autreUser->getName()
    ]);
    }




}
