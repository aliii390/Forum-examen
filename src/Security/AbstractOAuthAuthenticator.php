<?php 

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use App\Service\OAuthRegistrationService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait; 

abstract class AbstractOAuthAuthenticator extends OAuth2Authenticator
{
    use TargetPathTrait; // Le trait TargetPathTrait fournit la méthode getTargetPath qui permet de récupérer 
                        // l'URL vers laquelle l'utilisateur doit être redirigé après une authentification réussie. 
                        // C'est une fonctionnalité couramment utilisée dans Symfony pour la gestion de l'authentification.

    protected string $serviceName = '';

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly RouterInterface $router,
        private readonly UserRepository $userRepository,
        private readonly OAuthRegistrationService $registrationService
        )
    {
        
    }

    //  cette function 'supports' sers a  verifier si on est sur la route de check est que le service utiliser est un service que on peut supporter 
    public function supports(Request $request): ?bool
    {
        return 'auth_oauth_check' === $request->attributes->get('_route') && $request->get('service') === $this->serviceName;
        //si on est sur la page de check et le serviceName est égale a google  sa sera GooglAuthenticator qui prendra le relais 
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);

        // donc si il existe un target path l'user sera rediriger 
        if($targetPath){
            return new RedirectResponse($targetPath);
        }
        
        // sinon l'user on le redirige vers la page home 
        return new  RedirectResponse($this->router->generate('app_post'));
    }

    // cette function sert au cas ou si l'authentification ne marche pas
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()){
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        }

        // on redirige l'user vers le formulaire de connexion
        return new RedirectResponse($this->router->generate('app_register'));
    }


    // cette function sers a définir la logique de l'autentification
    public function authenticate(Request $request): SelfValidatingPassport
    {

        // cette variable contient l'AccessToken  qui est fourni par le provider (qui est google) apres la redirection sur la route auth_oauth_check
        $credentials = $this->fetchAccessToken($this->getClient());
        $ressourceOwner = $this->getResourceOwnerFromCredentials($credentials);

        // la je récup l'user depuis l'entity
        $user = $this->getUserFromResourceOwner($ressourceOwner, $this->userRepository );


        // dans le cas ou l'user n'existe pas : 
        if (null === $user){
            // creer son compte
            $user = $this->registrationService->persist($ressourceOwner);


        }

        return new SelfValidatingPassport(
          userBadge:  new UserBadge($user->getUserIdentifier(), fn () => $user),
            badges: [
                // le remember ser a ce rappelez de l'user un fois qui l'est connectez pour éviter qu'il ce reconecte a chaque fois 
                new RememberMeBadge()
            ]
        );




    }


    // cette function sers  recuperer les info de l'user 
    protected function getResourceOwnerFromCredentials(AccessToken $credentials) : ResourceOwnerInterface  //cette interface qui définit les information qu'on recup de google
    {
        return $this->getClient()->fetchUserFromToken($credentials);

    }



    private function getClient() : OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient($this->serviceName);
    }


    // on précise cette méthode ici qui ce trouver dans googleAuthenticator  pour dire quelle soit obligatoirement implémenter 
    abstract  function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner , UserRepository $userRepository): ?User;

}