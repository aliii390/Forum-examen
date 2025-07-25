<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PolitiqueController extends AbstractController
{
    #[Route('/politique', name: 'app_politique')]
    public function index(): Response
    {
        return $this->render('politique/index.html.twig', [
            'controller_name' => 'PolitiqueController',
        ]);
    }


      #[Route('/condition', name: 'app_condition')]
    public function condition(): Response
    {
        return $this->render('politique/condition.html.twig', [
            'controller_name' => 'PolitiqueController',
        ]);
    }
}
