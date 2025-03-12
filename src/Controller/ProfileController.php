<?php

namespace App\Controller;

use App\Form\UpdateInfoType;
use App\Interfaces\UpdateProfileInterface;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(EntityManagerInterface $entityManager , UpdateProfileInterface $updateProfilService , Request $request ,FileUploader $fileUploader ): Response
    {


        $user = $this->getUser();

        $form = $this->createForm(UpdateInfoType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $name = $form->get('name')->getData();
            // $plainPassword = $form->get('plainPassword')->getData(); 
            $email = $form->get('email')->getData();
          
            
            $updateProfilService->updateProfile($user, $name, $email);
            $this->addFlash('success', 'User updated successfully.');
            
            return $this->redirectToRoute('app_profile');
        }


        // le code pour upload une photo de profil 

        $photo = $form->get('photo')->getData();
             /**
         * @var User $user
         */
        if($photo){
            $postPhoto = $fileUploader->upload($photo, $user, 'photo', 'profile_pictures');
            $user->setPhoto($postPhoto);
        }


        return $this->render('profile/index.html.twig', [
          'updateForm' => $form->createView(),
        ]);
    }
}
