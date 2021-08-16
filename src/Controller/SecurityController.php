<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenApi\Annotations as OA;

class SecurityController extends AbstractController
{
    /**
     * @OA\Get(
     *      path="/api/token/github",
     *     summary="Github Oauth route to get a JWT token",
     *     operationId="getGithubToken",
     *     @OA\Response(
     *          response="200",
     *          description="A Github oauth route to get a JWT token"
     *          ),
     *     @OA\Response(
     *         response="default",
     *         description="an unexpected error"
     *   )
     * )
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
    public function getTokenAction(JWTTokenManagerInterface $JWTManager, UserInterface $user = null): JsonResponse
    {
        if ($user) {
            return new JsonResponse(['status' => 200, 'token' => $JWTManager->create($user)]);
        }

        return new JsonResponse(['status' => 401, 'message' => 'forbidden']);
    }

    /**
     * @OA\Get(
     *      path="/api/token/google",
     *     summary="Google Oauth route to get a JWT token",
     *     operationId="getGoogleToken",
     *     @OA\Response(
     *          response="200",
     *          description="A Google oauth route to get a JWT token"
     *          ),
     *     @OA\Response(
     *         response="default",
     *         description="an unexpected error"
     *   )
     * )
     * @Route("/api/token/google", name="connect_google_start")
     */
    public function connectGoogleAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect([
                'openid', 'https://www.googleapis.com/auth/userinfo.email'
            ]);
    }

    /**
     * @Route("/oauth/google/check", name="connect_google_check")
     */
    public function checkGoogleAction(ClientRegistry $clientRegistry)
    {
    }

    /**
     * @OA\Get(
     *      path="/api/token/facebook",
     *     summary="Facebook Oauth route to get a JWT token",
     *     operationId="getFacebookToken",
     *     @OA\Response(
     *          response="200",
     *          description="A Facefook oauth route to get a JWT token"
     *          ),
     *     @OA\Response(
     *         response="default",
     *         description="an unexpected error"
     *   )
     * )
     * @Route("/api/token/facebook", name="connect_facebook_start")
     */
    public function connectFacebookAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('facebook')
            ->redirect([
                'email'
            ]);
    }

    /**
     * @Route("/oauth/facebook/check", name="connect_facebook_check")
     */
    public function checkFacebookAction(ClientRegistry $clientRegistry)
    {
    }
}
