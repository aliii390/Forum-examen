<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdateInfoType;
use App\Interfaces\UpdateProfileInterface;
use App\Repository\PublicationRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
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

    //     #[Route('/publication/delete/{id}', name: 'app_publication_delete', methods: ['POST'])]

    // public function deletePublication(Publication $publication, EntityManagerInterface $entityManager): Response
    // {
    //     // verif si l'utilisateur est l'auteur de la publication
    //     if ($publication->getUser() !== $this->getUser()) {
    //         $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer cette publication');
    //         return $this->redirectToRoute('app_home');
    //     }

    //     try {
    //         // supprimer la photo associée si elle existe
    //         if ($publication->getPhoto()) {
    //             $photoPath = $this->getParameter('uploads_directory') . '/' . $publication->getPhoto();
    //             if (file_exists($photoPath)) {
    //                 unlink($photoPath);
    //             }
    //         }

    //         // supprimer tous les likes associés
    //         foreach ($publication->getPostLikers() as $like) {
    //             $entityManager->remove($like);
    //         }

    //         // supprimer la publication
    //         $entityManager->remove($publication);
    //         $entityManager->flush();

    //         $this->addFlash('success', 'Publication supprimée avec succès');
    //     } catch (\Exception $e) {
    //         $this->addFlash('error', 'Une erreur est survenue lors de la suppression');
    //     }

    //     return $this->redirectToRoute('app_home');
    // }


}
