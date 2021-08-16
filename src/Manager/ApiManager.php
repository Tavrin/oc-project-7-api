<?php

namespace App\Manager;

use App\Entity\User;
use App\Http\ApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiManager
{
    private const INDEX_DATA = [
        '_links' => [
            'self' => [
                'href' => '/api/',
                'description' => 'API root, here',
            ],
            'doc' => [
                'href' => '/api/doc.json',
                'description' => 'API documentation',
            ],
            'users' => [
                'href' => '/api/me/users',
                'description' => 'List the users associated with your account or create a new user',
            ],
            'phones' => ['href' => '/api/phones'],
            'token_retrieve' => ['href' => '/oauth/token'],
            'github_authentication' => ['href' => '/api/token/github'],
            'google_authentication' => ['href' => '/api/token/google'],
        ],
    ]
    ;

    private const DEFAULT_QUERIES = [
        'page' => 1,
        'limit' => null,
        'sort' => null,
    ]
    ;

    private QueryManager $queryManager;
    private MetadataManager $metadataManager;
    private NormalizerInterface $normalizer;
    private ValidatorInterface $validator;

    public function __construct(QueryManager $queryManager, MetadataManager $metadataManager, NormalizerInterface $normalizer, ValidatorInterface $validator)
    {
        $this->queryManager = $queryManager;
        $this->metadataManager = $metadataManager;
        $this->normalizer = $normalizer;
        $this->validator = $validator;
    }

    /**
     * @return array|\ArrayObject|bool|float|int|string|null
     *
     * @throws ExceptionInterface
     */
    public function getIndex()
    {
        return $this->normalizer->normalize(self::INDEX_DATA);
    }

    /**
     * @throws ExceptionInterface
     */
    public function getPhonesList(Request $request): array
    {
        $queries = $this->getQueries($request);
        $phoneEntities = $this->queryManager->setGetPhonesData($queries);
        $phones['items'] = $this->normalizer->normalize($phoneEntities, 'json', ['groups' => 'phone_list']);
        $queries['baseLink'] = '/api/phones';

        return $this->metadataManager->setGetPhonesMetadata($phones, $phoneEntities, $queries);
    }

    /**
     * @throws ExceptionInterface
     */
    public function getUsersList(Request $request, UserInterface $client): array
    {
        $queries = $this->getQueries($request);
        $userEntities = $this->queryManager->setGetUsersData($client, $queries);
        $users['items'] = $this->normalizer->normalize($userEntities, 'json', ['groups' => 'users_list']);
        $queries['baseLink'] = '/api/me/users';

        return $this->metadataManager->setUserListLinks($users, $userEntities, $queries);
    }

    public function setGetClientItemLinks(array $client): array
    {
        $client['_links'] = [
            'users' => ['href' => '/api/me/users'],
            ]
        ;

        foreach ($client['users'] as $key => $user) {
            $client['users'][$key] = $this->setSelfLink('/api/clients/', $user);
        }

        return $this->setSelfLink('/api/me', $client, false);
    }

    protected function setSelfLink(string $path, array $content, $setId = true): array
    {
        $setId ? $id = $content['id'] : $id = '';
        $content['_links']['self'] = ['href' => $path.$id, 'type' => 'GET'];

        return $content;
    }

    public function setNewUser(Request $request, $client)
    {
        $data = json_decode($request->getContent(), true);
        if (!empty($violations = $this->validateUserData($data))) {
            return new ApiResponse('new user validation error', $data, $violations, 400);
        }

        return $this->queryManager->addUser($data, $client);
    }

    public function removeUser(User $user, UserInterface $client): bool
    {
        if (!$client->hasUser($user)) {
            return false;
        }

        $this->queryManager->deleteUser($user);

        return true;
    }

    protected function validateUserData(?array $data): ?array
    {
        $validationResult = null;
        if (!isset($data['email'])) {
            $validationResult[] = 'missing required email field';
        }
        if (!isset($data['username'])) {
            $validationResult[] = 'missing required username field';
        }

        if ($validationResult) {
            return $validationResult;
        }

        $notBlankConstraint = new Assert\NotBlank();
        $emailConstraint = new Assert\Email();
        $emailConstraint->message = 'Invalid email address';
        $errors = $this->validator->validate(
            $data['email'],
            [
                $emailConstraint,
                $notBlankConstraint,
            ]
        );

        if (0 < count($errors)) {
            $validationResult[] = $errors[0]->getMessage();
        }

        $userNameLengthConstraint = new Assert\Length(['min' => 4, 'max' => 255]);
        $userNameLengthConstraint->minMessage = 'The username is too short. It should have {{ limit }} characters or more.';
        $userNameLengthConstraint->maxMessage = 'The username is too long. It should have {{ limit }} characters or less.';

        $errors = $this->validator->validate(
            $data['username'],
            [
                $userNameLengthConstraint,
                $notBlankConstraint,
            ]
        );

        if (0 < count($errors)) {
            $validationResult[] = $errors[0]->getMessage();
        }

        return $validationResult;
    }

    protected function getQueries(Request $request): array
    {
        $options = self::DEFAULT_QUERIES;

        if ($request->query->has('page') && 0 < $request->query->get('page')) {
            $options['page'] = (int) $request->query->get('page');
        }

        if ($request->query->has('limit') && 50 >= $request->query->get('limit') && 0 !== $request->query->get('limit')) {
            $options['limit'] = (int) $request->query->get('limit');
        }

        if ($request->query->has('sort')) {
            $options['sort'] = explode(',', $request->query->get('sort'));
        }

        return $options;
    }
}
