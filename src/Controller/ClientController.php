<?php

namespace App\Controller;

use App\Entity\User;
use App\Http\ApiResponse;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class ClientController extends ApiController
{
    /**
    /**
     * @OA\Get(
     *      path="/api/me",
     *     summary="Information about your account",
     *     operationId="getClientItemAction",
     *     @OA\Response(
     *          response="200",
     *          description="Successfully fetched your information"
     *      )
     * )
     * @Route("/api/me", name="get_client_item", methods={"GET", "OPTIONS"})
     *
     * @throws ExceptionInterface
     */
    public function getClientItemAction(Request $request): JsonResponse
    {
        $client = $this->getUser();
        $client = $this->normalizer->normalize($client, 'json', ['groups' => 'client_show']);
        $client = $this->apiManager->setGetClientItemLinks($client);

        return new JsonResponse(['status' => 200, 'message' => $client]);
    }

    /**
     * @OA\Get(
     *      path="/api/me/users",
     *     summary="Get a list of your users",
     *     operationId="getUsers",
     *     @OA\Response(
     *          response="200",
     *          description="A list of the users"
     *          ),
     *     @OA\Response(
     *         response="default",
     *         description="an unexpected error"
     *   )
     * ),
     * @OA\Post (
     *      path="/api/me/users",
     *     summary="Create a new user",
     *     operationId="addUser",
     *     @OA\Response(
     *          response="201",
     *          description="User created"
     *          ),
     *     @OA\Response(
     *         response="default",
     *         description="an unexpected error"
     *   )
     * )
     * @Route("/api/me/users", name="get_client_users", methods={"GET", "POST", "OPTIONS"})
     * @IsGranted("ROLE_USER", statusCode=401, message="Unauthorized")
     *
     * @throws ExceptionInterface
     */
    public function getClientUsersAction(Request $request): JsonResponse
    {
        $client = $this->getUser();
        $response = new JsonResponse();

        if ('POST' === $request->getMethod()) {
            $user = $this->apiManager->setNewUser($request, $client);
            if ($user instanceof ApiResponse) {
                return $user;
            }
            if ($user instanceof User) {
                $response->setData(['status' => 201, 'message' => 'success', 'data' => $this->normalizer->normalize($user, 'json', ['groups' => 'user_show'])]);
                $response->setStatusCode(201);
            } else {
                $response->setData(['status' => 500, 'message' => 'error', 'data' => $this->normalizer->normalize($user)]);
                $response->setStatusCode(500);
            }
        }
        if ('GET' === $request->getMethod()) {
            $users = $this->apiManager->getUsersList($request, $client);
            $response->setData(['status' => 200, 'message' => 'success', 'data' => $users]);
        }

        return $response;
    }

    /**
     * @OA\Get(
     *      path="/api/me/users/{id}",
     *     summary="Get information about one of your users",
     *     operationId="getUser",
     *     @OA\Response(
     *          response="200",
     *          description="Detailled information about a user"
     *          ),
     *     @OA\Response(
     *         response="default",
     *         description="an unexpected error"
     *   )
     * ),
     * @OA\Delete(
     *      path="/api/me/users/{id}",
     *     summary="Delete an user",
     *     operationId="deleteUser",
     *     @OA\Response(
     *          response="201",
     *          description="User successfully deleted"
     *          ),
     *     @OA\Response(
     *         response="default",
     *         description="an unexpected error"
     *   )
     * )
     * @Route("/api/me/users/{id}", name="get_client_user_item", methods={"GET", "DELETE", "OPTIONS"})
     * @IsGranted("ROLE_USER", statusCode=401, message="Unauthorized")
     *
     * @throws ExceptionInterface
     */
    public function getClientUserDetailsAction(Request $request, User $user): JsonResponse
    {
        $client = $this->getUser();
        $response = new JsonResponse();

        if ('DELETE' === $request->getMethod()) {
            $this->apiManager->removeUser($user, $client);
            $response->setStatusCode(204);
        }
        if ('GET' === $request->getMethod()) {
            $response->setData(['status' => 200, 'message' => 'success', 'data' => $this->normalizer->normalize($user, 'json', ['groups' => 'user_show'])]);
        }

        return $response;
    }
}
