<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Entity\Reponse;
use App\Form\CommentaireType;
use App\Form\ReponseType;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DetailController extends AbstractController
{
    #[Route('/detail/{id}', name: 'app_detail')]
    public function index(Publication $publication, Request $request, EntityManagerInterface $entityManager, CommentaireRepository $commentaireRepo): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setUser($this->getUser());
            $commentaire->setPublication($publication);
            $commentaire->setCreatedAt(new \DateTimeImmutable());
    
            $entityManager->persist($commentaire);
            $entityManager->flush();
    
            $this->addFlash('success', 'Votre commentaire a été ajouté avec succès!');
    
            return $this->redirectToRoute('app_detail', ['id' => $publication->getId()]);
        }
    
        // Gestion des réponses
        $reponse = new Reponse();
        $formReponse = $this->createForm(ReponseType::class, $reponse);
        $formReponse->handleRequest($request);
    
        if ($formReponse->isSubmitted() && $formReponse->isValid()) {
            $commentaireId = $request->request->get('commentaire_id');
            $commentaireParent = $commentaireRepo->find($commentaireId);
    
            if (!$commentaireParent) {
                throw $this->createNotFoundException('Commentaire introuvable');
            }
    
            $reponse->setUser($this->getUser());
            $reponse->setCommentaire($commentaireParent);
            $reponse->setCreatedAt(new \DateTimeImmutable());
    
            $entityManager->persist($reponse);
            $entityManager->flush();
    
            $this->addFlash('success', 'Votre réponse a été ajoutée avec succès!');
    
            return $this->redirectToRoute('app_detail', ['id' => $publication->getId()]);
        }
    
        // Récupération des commentaires avec leurs réponses
        $commentaires = $commentaireRepo->findBy(['publication' => $publication]);
    
        return $this->render('detail/index.html.twig', [
            'publication' => $publication,
            'commentaireType' => $form->createView(),
            'commentaireFin' => $commentaires,
            'reponseType' => $formReponse->createView(),
        ]);
    }

}