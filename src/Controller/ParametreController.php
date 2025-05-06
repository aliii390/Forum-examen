<?php

namespace App\Controller;

use App\Entity\AjoutAmi;
use App\Entity\CompteBloquer;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ParametreController extends AbstractController
{
    #[Route('/parametre', name: 'app_parametre')]
    public function index(CategoryRepository $categoryRepo): Response
    {

        /**
         * @var User $user
         */
        $user = $this->getUser();

         // pour affichez toout les user qu'on a bloquez
         $blockedUsers = $user->getCompteBloquers();

         // pour afficher tout les user qu'on a ajoutez 
         $ajoutUser = $user->getAjoutAmis();


        //  pour affichez toute les catégory 
         $categories = $categoryRepo->findAll();

        return $this->render('parametre/index.html.twig', [
            'profileUser' => $user,
            'users' => $blockedUsers,
            'amies' => $ajoutUser,
            'categories' => $categories
        ]);
    }


    // route pour voir tout les user bloquer
    #[Route('/users', name: 'app_users_list')]
    public function showAllUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('parametre/index.html.twig', [
            'users' => $users
        ]);
    }


        // route pour débloquer un user 
        #[Route('/unblock/{id}', name: 'app_unblock_user', methods: ['POST'])]
        public function unblock( int $id,User $user, Request $request, EntityManagerInterface $em): Response
        {

            $userDebloquer = $em->getRepository(User::class)->find($id) ;
            if ($this->isCsrfTokenValid('unblock' . $user->getId(), $request->request->get('_token'))) {
                // Supprimer l'entrée de blocage
                $block = $em->getRepository(CompteBloquer::class)->findOneBy([
                    'userBlocked' => $user,
                    // Optionnel : 'author' => $this->getUser(),
                ]);
    
                if ($block) {
                    $em->remove($block);
                    $em->flush();
                }
            }
    
            // message flash 
            $this->addFlash('debloquer', $userDebloquer->getName().' a bien été débloquer vous pouvez voir ces post ');
            return $this->redirectToRoute('app_parametre');
        }
       



        #[Route('/ami', name: 'app_ami_list')]
        public function voirAmi(UserRepository $userRepository): Response
        {
            $users = $userRepository->findAll();
    
            // de base dans le render y'avais sa users-list.html.twig je comprend pas pk ?? 
            return $this->render('parametre/index.html.twig', [
                'amies' => $users
            ]);
        }




    // route pour supprimer un user 
    #[Route('/supprimer/{id}', name: 'app_user_supprimer', methods: ['POST'])]
    public function userSupprimer(User $user, Request $request, EntityManagerInterface $em): Response
    {
        // /** @var User $currentUser */
        // $currentUser = $this->getUser();
        
        if ($this->isCsrfTokenValid('supprimer' . $user->getId(), $request->request->get('_token'))) {
            $block = $em->getRepository(AjoutAmi::class)->findOneBy([
                'userAjoutez' => $user,
            ]);
    
            if ($block) {
                $em->remove($block);
                $em->flush();
            }
        }
    
        $this->addFlash('supprimerAmi',   $user->getName() . ' a bien été supprimé de vos abonnements');
        
        // Récupérer le referer (page précédente)
        $referer = $request->headers->get('referer');
        
        // Si la suppression vient de la page profile personnel
        if (str_contains($referer, '/parametre')) {
            return $this->redirectToRoute('app_parametre');
        }
        
        // Sinon, rediriger vers le profil de l'utilisateur supprimé
        return $this->redirectToRoute('app_user_profile', [
            'name' => $user->getName()
        ]);
    }

}
