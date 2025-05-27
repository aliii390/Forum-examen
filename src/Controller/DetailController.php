<?php

namespace App\Controller;

use App\Entity\AjoutAmi;
use App\Entity\Commentaire;
use App\Entity\CompteBloquer;
use App\Entity\Publication;
use App\Entity\User;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use App\Repository\PublicationRepository;
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
    
            $this->addFlash('commentaire', 'Votre commentaire a été ajouté avec succès!');
    
            return $this->redirectToRoute('app_detail', ['id' => $publication->getId()]);
        }
    
        // Récupération des commentaires
        $commentaires = $commentaireRepo->findBy(['publication' => $publication]);
    
        return $this->render('detail/index.html.twig', [
            'publication' => $publication,
            'commentaireType' => $form->createView(),
            'commentaireFin' => $commentaires,
        ]);
    }




    // route pour supprimer un commentaire 

    #[Route('/commentaire/{id}/delete', name: 'commentaire_supprimer', methods: ['POST'])]
    public function delete(Commentaire $commentaire, EntityManagerInterface $em, Request $request): Response
    {
        // Protection CSRF
        if ($this->isCsrfTokenValid('delete_comment_' . $commentaire->getId(), $request->request->get('_token'))) {
            $publicationId = $commentaire->getPublication()->getId();
            $em->remove($commentaire);
            $em->flush();
    
            $this->addFlash('commentaireSupprimer', 'Le commentaire a bien été supprimé.');
        }
    
        return $this->redirectToRoute('app_detail', ['id' => $publicationId],);
    }
   
// {{ path('app_detail', {'id': question.id}) }}


  // route pour ajoutez en ami
    #[Route('/detailAjout/{id}', name: 'app_detail_ajout', methods: ['GET'])]
    public function ajoute(int $id, EntityManagerInterface $entityManager, Request $request ): Response
    {
        $user = $this->getUser();
        $autreUser = $entityManager->getRepository(User::class)->find($id);
        $publicationId = $request->query->get('redirectionApresAjoutPasConnecter');
        
        
        // dd( $publication);
          


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
            $this->addFlash('connectAjoutDetail', 'connectez vous pour ajoutez '. $autreUser->getName(). ' en abonnements');
            return $this->redirectToRoute('app_detail', ['id'=> $publicationId]);
           
        }


 
        // dd($user);

    }




     // route pour bloquer un user 
    #[Route('/detailBlock/{id}', name: 'app_detail_block', methods: ['GET'])]
    public function bloquer(int $id ,EntityManagerInterface $entityManager, Request $request ): Response
    {
        $user = $this->getUser();
        $autreUser = $entityManager->getRepository(User::class)->find($id);
        $publicationId = $request->query->get('redirectionApresBloquerPasConnectez');


        if($user){
             $compteBloquer = new CompteBloquer;
        $compteBloquer->setUser($user);
        $compteBloquer->setUserBlocked($autreUser);

        $entityManager->persist($compteBloquer);
        $entityManager->flush();

        // Ajout du message flash
        $this->addFlash('userBloquerConenctez',    $autreUser->getName() . ' a bien été bloqué');

        return $this->redirectToRoute('app_detail', ['id' => $publicationId] );
        }else{
        $this->addFlash('userBloquerDetailPasConnectez',  'connectez vous pour bloquez '. $autreUser->getName());
        return $this->redirectToRoute('app_detail', ['id'=> $publicationId]);
        }


       
    }

// 'app_detail', ['id' => $publication->getId()]

}