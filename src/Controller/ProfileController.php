<?php

namespace App\Controller;

use App\Form\UpdateInfoType;
use App\Interfaces\UpdateProfileInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(EntityManagerInterface $entityManager , UpdateProfileInterface $updateProfilService , Request $request): Response
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



        return $this->render('profile/index.html.twig', [
          'updateForm' => $form->createView(),
        ]);
    }
}
