<?php

namespace App\Controller;

use App\Entity\AjoutAmi;
use App\Entity\CompteBloquer;
use App\Entity\Publication;
use App\Entity\User;
use App\Form\UpdateInfoType;
use App\Interfaces\UpdateProfileInterface;
use App\Repository\AjoutAmiRepository;
use App\Repository\PublicationRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Orm\EntityPaginatorInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class ProfileController extends AbstractController
{

    // route pour quand l'user va voir son propre profile 
    #[Route('/profile', name: 'app_profile')]
    public function index(
        UpdateProfileInterface $updateProfilService,
        Request $request,
        FileUploader $fileUploader,
        PublicationRepository $publicationRepo,
        EntityManagerInterface $entityManager,
        // PaginatorInterface  $paginator, UserRepository $userRepository
        AjoutAmiRepository $ajoutRepo

    ): Response {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        // pour affichez toout les user qu'on a bloquez
        $blockedUsers = $user->getCompteBloquers();

        // pour afficher tout les user qu'on a ajoutez 
        $ajoutUser = $user->getAjoutAmis();


        $form = $this->createForm(UpdateInfoType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('name')->getData();
            // $plainPassword = $form->get('plainPassword')->getData(); 
            $email = $form->get('email')->getData();



            // le code pour upload une photo de profil 
            $photo = $form->get('photo')->getData();

            if ($photo) {
                $postPhoto = $fileUploader->upload($photo, $user, 'photo', 'uploads');
                $user->setPhoto($postPhoto);
            }

            // fin du code pour upload une photo



            $updateProfilService->updateProfile($user, $name, $email);
            $this->addFlash('profileModifiez', 'Votre compte a bien été mis a jour.');

            return $this->redirectToRoute('app_profile');
        }


        $publications = $publicationRepo->findBy(['user' => $user]);


        // pour afficher le nombre de question 
        $nombreQuestion = $publicationRepo->count(['user' => $user]);


        // pour afficher le nombre d'abonner
        $nombreAbonner = $ajoutRepo->count(['user' => $user]);
        // $nombreAbonner = $entityManager->getRepository(AjoutAmi::class)->count([
        //     'userAjoutez' => $user
        // ]);
        // dd($nombreAbonner);

        // $query = $userRepository->findUsersBlockedBy($user); // la méthode qu’on vient de créer

        // $pagination = $paginator->paginate(
        //     $query,
        //     $request->query->getInt('page', 1),
        //     5 // nombre d'éléments par page
        // );




        return $this->render('profile/personal-profile.html.twig', [
            'profileUser' => $user,
            'updateForm' => $form->createView(),
            'publication' => $publications,
            'users' => $blockedUsers,
            'nombreQuestion' => $nombreQuestion,
            'amies' => $ajoutUser,
            'nombreAbonner' => $nombreAbonner,
            // 'pagination' => $pagination

        ]);
    }


    // route pour quand l'user va voir le profil d'un autre user 
    #[Route('/user/{name}', name: 'app_user_profile')]
    public function connect(string $name, UserRepository $userRepository, PublicationRepository $publicationRepo, EntityManagerInterface $entityManager, AjoutAmiRepository $ajoutRepo): Response
    {

        $user = $userRepository->findOneBy(['name' => $name]);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        if ($user === $this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        $utilisateurConnectéLeSuis = $ajoutRepo->findBy(['user' => $this->getUser(), 'userAjoutez' => $user]);

        // pour afficher le nombre de question quand on est deconnectez de  notre compte
        $nombreQuestion = $publicationRepo->count(['user' => $user]);

        //pour afficher le nombre d'abonner 
        $nombreAbonner = $ajoutRepo->count(['user' => $user]);


        $publications = $user->getPublications();

        return $this->render('profile/foreign-profile.html.twig', [
            'profileUser' => $user,
            'publication' => $publications,
            'nombreQuestion' => $nombreQuestion,
            'nombreAbonner' => $nombreAbonner,
            'utilisateurConnectéLeSuis' => $utilisateurConnectéLeSuis ? true : false
        ]);
    }


    // route pour supprimez une question 
    #[Route('/post/{id}/delete', name: 'post_delete', methods: ['POST'])]
    public function delete(Publication $publication, EntityManagerInterface $em, Request $request): Response
    {
        // Protection CSRF
        if ($this->isCsrfTokenValid('delete_post_' . $publication->getId(), $request->request->get('_token'))) {
            $em->remove($publication);
            $em->flush();

            $this->addFlash('supprimer', 'Le post a bien été supprimé.');
        }

        return $this->redirectToRoute('app_profile');
    }



    // route pour voir tout les user bloquer
    #[Route('/users', name: 'app_users_list')]
    public function showAllUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('profile/index.html.twig', [
            'users' => $users
        ]);
    }



    // route pour débloquer un user 
    #[Route('/unblock/{id}', name: 'app_unblock_user', methods: ['POST'])]
    public function unblock(User $user, Request $request, EntityManagerInterface $em): Response
    {
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
        $this->addFlash('debloquer', 'L\'utilisateur a bien été débloquer vous pouvez voir ces post ');
        return $this->redirectToRoute('app_profile');
    }


    // route pour voir tout les amis ajoutez 


    #[Route('/ami', name: 'app_ami_list')]
    public function voirAmi(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        // de base dans le render y'avais sa users-list.html.twig je comprend pas pk ?? 
        return $this->render('profile/index.html.twig', [
            'amies' => $users
        ]);
    }

    // route pour supprimer un user 
    #[Route('/supprimer/{id}', name: 'app_user_supprimer', methods: ['POST'])]
    public function userSupprimer(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('supprimer' . $user->getId(), $request->request->get('_token'))) {
            // Supprimer l'entrée de blocage
            $block = $em->getRepository(AjoutAmi::class)->findOneBy([
                'userAjoutez' => $user,
                // Optionnel : 'author' => $this->getUser(),
            ]);

            if ($block) {
                $em->remove($block);
                $em->flush();
            }
        }

        // message flash 

        $this->addFlash('supprimerAmi', 'L\'utilisateur a bien été supprimez');
        return $this->redirectToRoute('app_profile');
    }



    // route pour supprimez le compte
    #[Route('/profile/supprimer/{id}', name: 'app_profile_supprimer')]
    public function supprimer(
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Vérifie que l'utilisateur connecté est le même que celui qu'on veut supprimer
        if ($user !== $currentUser) {
            $this->addFlash('danger', 'Vous n\'êtes pas autorisé à supprimer ce compte.');
            return $this->redirectToRoute('app_profile');
        }

        $user->setDeletedAt(new \DateTimeImmutable());
        $user->setRoles(['ROLE_DELETED']);
        $entityManager->flush();

        return $this->redirectToRoute('app_register');
    }


    // route pour ajoutez en ami
    #[Route('/ajout/{id}', name: 'app_ajout', methods: ['GET'])]
    public function ajoute(int $id, EntityManagerInterface $entityManager, AjoutAmiRepository $ajoutAmiRepository, UserRepository $userRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        /** @var User $autreUser */
        $autreUser = $userRepository->find($id);

        if (!$autreUser) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $dejaAbonné = $ajoutAmiRepository->findBy(['user' => $user, 'userAjoutez' => $autreUser]);
        if ($dejaAbonné) {
            $this->addFlash('ajoutMarche', 'Vous avez déjà ajouté l\'utilisateur en ami.');
            return $this->redirectToRoute('app_user_profile', ['name' => $autreUser->getName()]);
        }

        $ajoutAmi = new AjoutAmi();
        $ajoutAmi->setUser($user);
        $ajoutAmi->setUserAjoutez($autreUser);

        $entityManager->persist($ajoutAmi);
        $entityManager->flush();

        $this->addFlash('ajoutMarche', 'Vous avez bien ajouté l\'utilisateur en ami.');

        return $this->redirectToRoute('app_user_profile', ['name' => $autreUser->getName()]);
    }
}
