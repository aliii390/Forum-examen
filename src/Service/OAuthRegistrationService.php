<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Service qui gère l'enregistrement des utilisateurs via OAuth (Google)
 * 'readonly' signifie que les propriétés ne peuvent pas être modifiées après construction
 */
final readonly class OAuthRegistrationService
{
    /**
     * Constructeur du service
     * @param UserRepository $userRepository - Pour interagir avec la base de données des utilisateurs
     * @param UserPasswordHasherInterface $passwordHasher - Pour hasher les mots de passe
     */
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }
    
    /**
     * Crée et persiste un nouvel utilisateur à partir des données Google
     * @param ResourceOwnerInterface $resourceOwner - Les données de l'utilisateur Google
     * @return User - Le nouvel utilisateur créé
     * @throws \RuntimeException si les données ne proviennent pas de Google
     */
    public function persist(ResourceOwnerInterface $resourceOwner): User
    {
        // Vérifie que les données proviennent bien de Google
        if (!($resourceOwner instanceof GoogleUser)) {
            throw new \RuntimeException('Expected GoogleUser instance');
        }
    
        // Création d'un nouvel utilisateur
       
        $user = new User();
        // Attribution de l'email et de l'ID Google
        $user->setEmail($resourceOwner->getEmail())
        ->setName($resourceOwner->getName())
        ->setPhoto($resourceOwner->getAvatar())
      
        // ->setPhoto($resourceOwner->getPhoto())
        ->setGoogleId((int) $resourceOwner->getId());
     
        // Génération d'un mot de passe aléatoire de 32 caractères 
        $randomPassword = bin2hex(random_bytes(16));
        // Hashage du mot de passe pour sécurisation
        $hashedPassword = $this->passwordHasher->hashPassword($user, $randomPassword);
        $user->setPassword($hashedPassword);
    
        // Sauvegarde de l'utilisateur en base de données (flush=true pour persistance immédiate)
        $this->userRepository->add($user, true);
        return $user;
    }
}