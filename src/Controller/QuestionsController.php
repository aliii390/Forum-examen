<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Publication;
use App\Form\PublicationType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuestionsController extends AbstractController
{
    #[Route('/questions', name: 'app_questions')]
    public function index(CategoryRepository $categoryRepo , Request $request , EntityManagerInterface $entityManager): Response
    {
        $category =  $categoryRepo->findAll();
        $form = $this->createForm(PublicationType::class);
        $form->handleRequest($request);
        $question = new Publication();

        if($form->isSubmitted() && $form->isValid()){
            dd($form);
           $question->setUser($this->getUser());
        
           $entityManager->persist($question);
           $entityManager->flush();
           
        }
        // dd($form);
        return $this->render('questions/index.html.twig', [
            'category' => $category,
            'publicationType' => $form->createView(),
        ]);
    }
}
