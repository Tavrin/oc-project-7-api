<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/api/token/github", name="connect_github_start")
     */
    public function connectGithubAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('github')
            ->redirect([
                'read:user', 'user:email'
            ]);
    }

    /**
     * @Route("/oauth/github/check", name="connect_github_check")
     */
    public function checkGithubAction(ClientRegistry $clientRegistry)
    {
    }

    /**
     * @Route("/oauth/token", name="connect_get_token")
     */
    public function getTokenAction(JWTTokenManagerInterface $JWTManager, UserInterface $user = null)
    {
        if ($user) {
            return new JsonResponse(['status' => 200, 'token' => $JWTManager->create($user)]);
        }

        return new JsonResponse(['status' => 401, 'message' => 'forbidden']);
    }
}
