<?php

namespace App\Manager;

use App\Entity\Client;

class MetadataManager
{
    protected function setSelfLink(string $path, array $content): array
    {
        $content['_links']['self'] = ["href" => $path.$content['id'], "type" => 'GET'];
        return $content;
    }

    public function setGetPhonesLinks(array $phones): array
    {
        foreach ($phones as $key => $phone) {
            $phones[$key] = $this->setSelfLink('/api/phones/', $phone);
        }

        return $phones;
    }

    public function setGetClientsLinks(array $clients): array
    {
        foreach ($clients as $key => $client) {
            $clients[$key] = $this->setSelfLink('/api/clients/', $client);
        }

        return $clients;
    }

    public function setUserListLinks(Client $client, array $users): array
    {
        foreach ($users as $key => $user) {
            $users[$key] = $this->setSelfLink('/api/clients/'.$client->getId().'/users/', $user);
        }
        return $users;
    }
}