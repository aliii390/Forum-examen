<?php

namespace App\Controller;

use App\Entity\PostLiker;
use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    #[Route('/like/{id}', name: 'app_like')]
    public function like(Publication $publication, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour aimer une publication');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur a déjà aimé la publication
        $postLiker = $entityManager->getRepository(PostLiker::class)->findOneBy([
            'user' => $this->getUser(),
            'publication' => $publication
        ]);

        if ($postLiker) {
            // Si oui, on retire le like
            $entityManager->remove($postLiker);
            $this->addFlash('aimePlus', 'Vous n\'aimez plus cette publication');
        } else {
            // Si non, on ajoute le like
            $postLiker = new PostLiker();
            $postLiker->setUser($this->getUser());
            $postLiker->setPublication($publication);
            $entityManager->persist($postLiker);
            $this->addFlash('aime', 'Vous aimez cette publication');
        }

        $entityManager->flush();

        // Rediriger vers la page précédente
        return $this->redirect($this->generateUrl('app_home'));
    }
}