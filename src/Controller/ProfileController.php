<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdateInfoType;
use App\Interfaces\UpdateProfileInterface;
use App\Repository\PublicationRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(EntityManagerInterface $entityManager, UpdateProfileInterface $updateProfilService, 
                        Request $request, FileUploader $fileUploader , PublicationRepository $publicationRepo): Response
    {
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



        return $this->render('profile/index.html.twig', [
            'updateForm' => $form->createView(),
            'publication' => $publications,
        ]);
    }
}
