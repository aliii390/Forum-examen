<?php

namespace App\Controller;

use App\Entity\Reponse;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    // les SCOPES ce sont les informations qu'on veut récuperer du provider (google) comme par exemple l'email etc...  met la je met un tableau vide pour prendres tout les scopes par défaut 
public const SCOPES = [
    'google' => [
        'email',
        'profile',
        'openid'
    ]
];


    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }



    // les 2 route en dessous c'est pour la connexion avec google 
    #[Route(path: '/oauth/connect/{service}', name: 'auth_oauth_connect', methods: ['GET'])]
    public function connect(string $service, ClientRegistry $clientRegistry): RedirectResponse{

        // la on verifie  le service ou l'user essaye de se connectez existe vraiment et ducoup si sa existe pas sa lui envoie une erreur 
        if(! in_array($service, array_keys(self::SCOPES), true)){
            throw $this->createNotFoundException('Le service d\'authentification "'.$service.'" n\'existe pas.');
        }


   
        return $clientRegistry->getClient($service)->redirect(self::SCOPES[$service], [
            'prompt' => 'select_account'  // Force l'affichage de la page de sélection de compte (car  j'ai eu un problème quand je me me connectais avec google sa me redirigais diriect sur mon site et pas sur la page de google voir dans knpu )
        ]);

    }


    /**
     * Point de terminaison de callback OAuth pour gérer les réponses d'authentification des fournisseurs OAuth.
     * Cette route est appelée par le service OAuth après une authentification réussie de l'utilisateur.
     * Le paramètre {service} identifie quel fournisseur OAuth est utilisé (ex: Google, Facebook, etc.)
     * 
     * @return Response Retourne un code de statut 200 OK après une authentification OAuth réussie
     */

    #[Route(path: '/oauth/check/{service}', name: 'auth_oauth_check', methods: ['GET'])]

    public function check(): Response
    {
        return new  Response(status: 200);
    }
}
