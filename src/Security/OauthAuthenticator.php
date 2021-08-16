<?php

namespace App\Security;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class OauthAuthenticator extends SocialAuthenticator
{
    private const TYPE_GOOGLE = 'google';
    private const TYPE_GITHUB = 'github';
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $em;
    private RouterInterface $router;
    private ?string $type = null;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }

    public function supports(Request $request): bool
    {
        if ($request->attributes->get('_route') === 'connect_google_check') {
            $this->type = self::TYPE_GOOGLE;
            return true;
        } elseif ($request->attributes->get('_route') === 'connect_github_check') {
            $this->type = self::TYPE_GITHUB;
            return true;
        }

        return false;
    }

    public function getCredentials(Request $request)
    {
        if (self::TYPE_GOOGLE === $this->type) {
            return $this->fetchAccessToken($this->getGoogleClient());
        } elseif (self::TYPE_GITHUB === $this->type) {
            return $this->fetchAccessToken($this->getGithubClient());
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $existingUser = null;

        if (self::TYPE_GITHUB === $this->type) {
            $user = $this->getGithubClient()
                ->fetchUserFromToken($credentials);
            $email = $user->getEmail();

            $existingUser = $this->em->getRepository(Client::class)
                ->findOneBy(['githubId' => $user->getId()]);
            if ($existingUser) {
                return $existingUser;
            }

            $existingUser = $this->em->getRepository(Client::class)
                ->findOneBy(['email' => $email]);

            if ($existingUser) {
                $existingUser->setGithubId($user->getId());
                $this->em->flush();
            }
        } elseif (self::TYPE_GOOGLE === $this->type) {
            $user = $this->getGoogleClient()
                ->fetchUserFromToken($credentials);
            $email = $user->getEmail();

            $existingUser = $this->em->getRepository(Client::class)
                ->findOneBy(['googleId' => $user->getId()]);
            if ($existingUser) {
                return $existingUser;
            }

            $existingUser = $this->em->getRepository(Client::class)
                ->findOneBy(['email' => $email]);

            if ($existingUser) {
                $existingUser->setGoogleId((int) $user->getId());
                $this->em->flush();
            }
        }


        return $existingUser;
    }

    private function getGoogleClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry
            ->getClient('google');
    }

    private function getGithubClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry
            ->getClient('github');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        // change "app_homepage" to some route in your app
        $targetUrl = $this->router->generate('connect_get_token');

        return new RedirectResponse($targetUrl);

        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new JsonResponse(['status'=> 403, 'error' => $message], Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            '/api/token/github', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}