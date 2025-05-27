<?php

namespace App\Controller;

use App\Entity\AjoutAmi;
use App\Entity\CompteBloquer;
use App\Entity\PostLiker;
use App\Entity\Publication;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PublicationRepository $publicationRepository, CategoryRepository $categoryRepository): Response
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
    #[Route('/block/{id}', name: 'app_home_block', methods: ['GET'])]
    public function bloquer(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $autreUser = $entityManager->getRepository(User::class)->find($id);

        if ($user) {
            $compteBloquer = new CompteBloquer;
            $compteBloquer->setUser($user);
            $compteBloquer->setUserBlocked($autreUser);
            $entityManager->persist($compteBloquer);
            $entityManager->flush();


            // Ajout du message flash
            $this->addFlash('userBloquer',    $autreUser->getName() . ' a bien été bloqué');

            return $this->redirectToRoute('app_home');
        } else {
            $this->addFlash('connectBloquer', 'connectez vous pour bloquer ' . $autreUser->getName());
            return $this->redirectToRoute('app_home');
        }
    }


    // route pour ajoutez en ami
    #[Route('/ajout/{id}', name: 'app_home_ajout', methods: ['GET'])]
    public function ajoute(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $autreUser = $entityManager->getRepository(User::class)->find($id);


        if ($user) {
            $ajoutAmi = new AjoutAmi;
            $ajoutAmi->setUser($user);
            $ajoutAmi->setUserAjoutez($autreUser);

            $entityManager->persist($ajoutAmi);
            $entityManager->flush();

            // Ajout du message flash
            $this->addFlash('ajoutMarche', 'Vous avez bien ajouté ' . $autreUser->getName() . ' en ami');
            return $this->redirectToRoute('app_user_profile', [
                'name' => $autreUser->getName()
            ]);
        } else {
            $this->addFlash('connectAjout', 'connectez vous pour ajoutez '. $autreUser->getName() . ' dans vos abonnements');
            return $this->redirectToRoute('app_home');
        }



        // dd($user);





    }


    // route pour liker des post 
    #[Route('/like/{id}', name: 'app_like', methods: ['POST'])]
    public function like(Publication $publication, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 403);
        }


        $postLikerRepo = $entityManager->getRepository(PostLiker::class);

        $postLiker = $postLikerRepo->findOneBy([
            'user' => $this->getUser(),
            'publication' => $publication
        ]);

        if ($postLiker) {
            $entityManager->remove($postLiker);
            // $message = 'Like retiré';
        } else {
            $postLiker = new PostLiker();
            $postLiker->setUser($this->getUser());
            $postLiker->setPublication($publication);
            $entityManager->persist($postLiker);
            // $message = 'Like ajouté';
        }

        $entityManager->flush();

        // Compter les likes
        $likeCount = $postLikerRepo->count(['publication' => $publication]);

        return new JsonResponse([
            'likes' => $likeCount,
            // 'message' => $message
        ]);
    }
}
