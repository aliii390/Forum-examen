<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DetailController extends AbstractController
{
    #[Route('/detail/{id}', name: 'app_detail')]
    public function index(Publication $publication , Request $request, EntityManagerInterface $entityManager , CommentaireRepository $commentaireRepo ): Response
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


    // sa c'est pour affichez les commentaires apres l'envoi du formulaire
    $commentaires = $commentaireRepo->findBy(['publication' => $publication]);

    return $this->render('detail/index.html.twig', [
        'publication' => $publication,
        'commentaireType' => $form->createView(),
        'commentaireFin' => $commentaires
    ]);
    }

}