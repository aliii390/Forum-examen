<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;



class GoogleAuthenticator extends AbstractOAuthAuthenticator
{

    protected string $serviceName = 'google';

    // Cette méthode sert à :
    // Récupérer ou créer un utilisateur dans votre application en se basant sur les informations fournies par Google
    // Faire le lien entre un compte Google et un compte utilisateur de votre application

    public function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner, UserRepository $userRepository): ?User
    {
        if (!($resourceOwner instanceof GoogleUser)) {
            throw new \RuntimeException("expecting google user");
        }

        if (false === ($resourceOwner->toArray()['email_verified'] ?? null)) {
            throw new AuthenticationException("email pas verifier");
        }

        return $userRepository->findOneBy([
            'googleId' => $resourceOwner->getId(),
            'email' => $resourceOwner->getEmail(),
            'name' => $resourceOwner->getName()
        ]);
    }
}
