<?php

namespace App\Controller;

use App\Entity\CompteBloquer;
use App\Entity\User;
use App\Form\UpdateInfoType;
use App\Interfaces\UpdateProfileInterface;
use App\Repository\PublicationRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(
        UpdateProfileInterface $updateProfilService,
        Request $request,
        FileUploader $fileUploader,
        PublicationRepository $publicationRepo
    ): Response {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $blockedUsers = $user->getCompteBloquers();


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
            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('app_profile');
        }


        $publications = $publicationRepo->findBy(['user' => $user]);



        return $this->render('profile/personal-profile.html.twig', [
            'profileUser' => $user,
            'updateForm' => $form->createView(),
            'publication' => $publications,
            'users'=> $blockedUsers
        ]);
    }


    #[Route('/user/{name}', name: 'app_user_profile')]
    public function connect(string $name, UserRepository $userRepository): Response
    {

        $user = $userRepository->findOneBy(['name' => $name]);
    
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        if ($user === $this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        $publications = $user->getPublications();

        return $this->render('profile/foreign-profile.html.twig', [
            'profileUser' => $user,
            'publication' => $publications,
        ]);
    }


    // route pour voir tout les user bloquer
    #[Route('/users', name: 'app_users_list')]
    public function showAllUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('profile/users-list.html.twig', [
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

       // Ajout du message flash
       $this->addFlash('debloquer', 'L\'utilisateur a bien été débloquer vous pouvez voir ces post ');
    return $this->redirectToRoute('app_profile');
}

}
