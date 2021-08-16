<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    /**
     * ApiResponse constructor.
     *
     * @param mixed $data
     */
    public function __construct(string $message, $data = null, array $errors = [], int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct($this->format($message, $data, $errors, $status), $status, $headers, $json);
    }

    /**
     * Format the API response.
     *
     * @param mixed $data
     */
    private function format(string $message, $data = null, array $errors = [], $status = null): array
    {
        if (null === $data) {
            $data = new \ArrayObject();
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}
