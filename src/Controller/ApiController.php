<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Phone;
use App\Manager\ApiManager;
use App\Manager\QueryManager;
use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiController extends AbstractController
{
    protected ?NormalizerInterface $normalizer = null;
    protected ApiManager $apiManager;

    public function __construct(NormalizerInterface $normalizer, ApiManager $apiManager)
    {
        $this->apiManager = $apiManager;
        $this->normalizer = $normalizer;
    }

    /**
     * @Route("/api/phones", name="get_phones", methods={"GET", "OPTIONS"})
     */
    public function getPhonesAction(Request $request, PhoneRepository $phoneRepository, QueryManager $queryManager): JsonResponse
    {
        $phones = $this->apiManager->getPhonesList();
        return new JsonResponse(['status' => 200, 'message' => $phones]);
    }

    /**
     * @Route("/api/phones/{id}", name="get_phone_item", methods={"GET", "OPTIONS"})
     */
    public function getPhoneItemAction(Phone $phone): JsonResponse
    {
       return new JsonResponse(['status' => 200, 'message' => $this->normalizer->normalize($phone)]);
    }

    /**
     * List the clients or creates a client
     *
     * This call can list the clients with a GET request or create one with a POST request
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Client::class, groups={"client_list"}))
     *     )
     * )
     * @Route("/api/clients", name="get_clients", methods={"GET", "OPTIONS"})
     * @throws ExceptionInterface
     */
    public function getClientsAction(Request $request): JsonResponse
    {
        $response = new JsonResponse();
        $clients = $this->apiManager->getClientsList();
        $response->setData(['status' => 200, 'message' => $clients]);

        return $response;
    }

    /**
     * @Route("/api/clients/{id}", name="get_client_item", methods={"GET", "PUT", "DELETE", "OPTIONS"})
     * @throws ExceptionInterface
     */
    public function getClientItemAction(Request $request, Client $client): JsonResponse
    {
        $response = new JsonResponse();
        if ('GET' === $request->getMethod()) {
            $client = $this->normalizer->normalize($client, 'json', ['groups' => 'client_show']);
            $client = $this->apiManager->setGetClientItemLinks($client);
            $response->setData(['status' => 200, 'message' => $client]);
        }

        return $response;
    }

    /**
     * @Route("/api/clients/{id}/users", name="get_client_users", methods={"GET", "POST", "OPTIONS"})
     * @throws ExceptionInterface
     */
    public function getClientUsersAction(Request $request, Client $client): JsonResponse
    {
        $response = new JsonResponse();
        if ('POST' === $request->getMethod()) {
            $user = $this->apiManager->setNewUser($request);
            if (false !== $client) {
                $response->setData(['status' => 201, 'message' => $client]);
            } else {
                $response->setData(['status' => 500, 'message' => 'error']);
            }
        }
        if ('GET' === $request->getMethod()) {
            $users = $this->apiManager->getUsersList($client);
            $response->setData(['status' => 200, 'message' => $users]);
        }

        return $response;
    }
}
