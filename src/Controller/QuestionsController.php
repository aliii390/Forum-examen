<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Form\CommentaireType;
use App\Form\PublicationType;
use App\Repository\CategoryRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuestionsController extends AbstractController
{
    #[Route('/questions', name: 'app_questions')]
    public function index(CategoryRepository $categoryRepo , Request $request , EntityManagerInterface $entityManager , FileUploader $fileUploader): Response
    {
      

        

        $category = $categoryRepo->findAll();
        $question = new Publication();
        
        $form = $this->createForm(PublicationType::class, $question);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
           $question->setUser($this->getUser());
           
           $question->setTitle($form->get('title')->getData());
           $question->setDescription($form->get('description')->getData());
           $question->setCategory($form->get('category')->getData());

      
           // rajoutez l'upload de photo 

            /**
         * @var User $user
         */
        $user = $this->getUser();

         $photo = $form->get('photo')->getData();

            if ($photo) {
                // nickel le dd passe bien dans le if et récupere bien l'image 
                // dd($photo);
                $postPhoto = $fileUploader->upload($photo, $user, 'photo', 'uploads');
                $user->setPhoto($postPhoto);
            }

        //modifiez le code  upload

           $entityManager->persist($question);
           $entityManager->flush();
           
           return $this->redirectToRoute('app_home');
        }

        return $this->render('questions/index.html.twig', [
            'category' => $category,
            'publicationType' => $form->createView(),
        ]);
    }
}