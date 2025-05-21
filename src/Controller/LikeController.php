<?php

namespace App\Controller;

use App\Entity\PostLiker;
use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    #[Route('/like/{id}', name: 'app_like', methods: ['POST'])]
public function like(Publication $publication, EntityManagerInterface $entityManager): JsonResponse
{
    if (!$this->getUser()) {
        return new JsonResponse(['error' => 'Non connecté'], 403);
    }

    $postLikerRepo = $entityManager->getRepository(PostLiker::class);

    $postLiker = $postLikerRepo->findOneBy([
        'user' => $this->getUser(),
        'publication' => $publication
    ]);

    if ($postLiker) {
        $entityManager->remove($postLiker);
        $message = 'Like retiré';
    } else {
        $postLiker = new PostLiker();
        $postLiker->setUser($this->getUser());
        $postLiker->setPublication($publication);
        $entityManager->persist($postLiker);
        $message = 'Like ajouté';
    }

    $entityManager->flush();

    // Compter les likes
    $likeCount = $postLikerRepo->count(['publication' => $publication]);

    return new JsonResponse([
        'likes' => $likeCount,
        'message' => $message
    ]);
}

}