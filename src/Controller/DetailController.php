<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DetailController extends AbstractController
{
    #[Route('/detail/{id}', name: 'app_detail')]
    public function index(Publication $publication , Request $request, EntityManagerInterface $entityManager): Response
    {

// pour postez un commentaire
$commentaire = new Commentaire();
$formCommentaire =  $this->createForm(CommentaireType::class, $commentaire);
$formCommentaire->handleRequest($request);

if($formCommentaire->isSubmitted() && $formCommentaire->isValid()){
    $commentaire->setUser($this->getUser());

    $commentaire->setMessage($formCommentaire->get('message')->getData());
    $entityManager->persist($commentaire);
    $entityManager->flush();
}

// fin du code pour postez un commentaire 

        return $this->render('detail/index.html.twig', [
            'publication' => $publication,
            'commentaireType' =>  $formCommentaire->createView(),

        ]);
    }
}
