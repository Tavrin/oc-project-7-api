<?php

namespace App\Controller;

use App\Manager\ApiManager;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @OA\Get(
     *      path="/api",
     *     summary="The index of the API, with some information about some routes",
     *     operationId="getIndex",
     *     @OA\Response(
     *          response="200",
     *          description="An index of the API"
     *          ),
     *     @OA\Response(
     *         response="default",
     *         description="an unexpected error"
     *   )
     * )
     * @Route("/api", name="api_index", methods={"GET", "OPTIONS"})
     *
     * @throws ExceptionInterface
     */
    public function getIndexAction(): JsonResponse
    {
        $index = $this->apiManager->getIndex();

        return new JsonResponse(['status' => 'success', 'code' => 200, 'data' => $index]);
    }
}
