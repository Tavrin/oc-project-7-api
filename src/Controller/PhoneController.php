<?php

namespace App\Controller;

use App\Entity\Phone;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use OpenApi\Annotations as OA;

class PhoneController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/api/phones",
     *     summary="Get a list the available phones",
     *     operationId="getPhones",
     *     @OA\Response(
     *          response="200",
     *          description="A list of the phones"
     *          ),
     *     @OA\Response(
     *         response="default",
     *         description="an unexpected error"
     *   )
     * )
     * @Route("/api/phones", name="get_phones", methods={"GET", "OPTIONS"})
     * @throws ExceptionInterface
     */
    public function getPhonesAction(Request $request): JsonResponse
    {
        $phones = $this->apiManager->getPhonesList($request);
        return new JsonResponse(['status' => 'success', 'code' => 200, 'data' => $phones]);
    }

    /**
     * @OA\Get(
     *      path="/api/phones/{id}",
     *     summary="Get a detailled description about a phone",
     *     operationId="getPhone",
     *     @OA\Response(
     *          response="200",
     *          description="Details about a phone"
     *          ),
     *     @OA\Response(
     *         response="default",
     *         description="an unexpected error"
     *   )
     * )
     * @Route("/api/phones/{id}", name="get_phone_item", methods={"GET", "OPTIONS"})
     * @throws ExceptionInterface
     */
    public function getPhoneItemAction(Phone $phone): JsonResponse
    {
        return new JsonResponse(['status' => 200, 'message' => $this->normalizer->normalize($phone)]);
    }
}