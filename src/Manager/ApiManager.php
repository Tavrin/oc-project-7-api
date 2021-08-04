<?php

namespace App\Manager;

use App\Entity\Client;
use http\Env\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiManager
{
    private QueryManager $queryManager;
    private MetadataManager $metadataManager;
    private NormalizerInterface $normalizer;
    public function __construct(QueryManager $queryManager, MetadataManager $metadataManager, NormalizerInterface $normalizer)
    {
        $this->queryManager = $queryManager;
        $this->metadataManager = $metadataManager;
        $this->normalizer = $normalizer;
    }

    public function getPhonesList(): array
    {
        $phones = $this->queryManager->setGetPhonesData();
        $phones = $this->normalizer->normalize($phones, 'json', ['groups' => 'phone_list']);
        return $this->metadataManager->setGetPhonesLinks($phones);
    }

    public function getClientsList(): array
    {
        $clients = $this->queryManager->setGetClientsData();
        $clients = $this->normalizer->normalize($clients, 'json', ['groups' => 'client_list']);
        return $this->metadataManager->setGetClientsLinks($clients);
    }

    public function getUsersList(Client $client): array
    {
        $users = $this->queryManager->setGetUsersData($client);
        $users = $this->normalizer->normalize($users, 'json', ['groups' => 'users_list']);
        return $this->metadataManager->setUserListLinks($client, $users);
    }

    public function setGetClientItemLinks(array $client): array
    {
        $client['_links'] = [
            "users" => ["href" => '/api/clients/'.$client['id'].'/users']
            ]
        ;

        foreach ($client['users'] as $key => $user) {
            $client['users'][$key] = $this->setSelfLink('/api/clients/', $user);
        }
        return $this->setSelfLink('/api/clients/', $client);
    }

    protected function setSelfLink(string $path, array $content): array
    {
        $content['_links']['self'] = ["href" => $path.$content['id'], "type" => 'GET'];
        return $content;
    }

    public function setNewUser(Request $request)
    {
        $this->queryManager->addClient();
    }
}