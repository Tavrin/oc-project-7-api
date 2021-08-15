<?php

namespace App\Manager;

use App\Entity\Client;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Security\Core\User\UserInterface;

class MetadataManager
{
    protected function setSelfLink(string $path, array $content): array
    {
        $content['_links']['self'] = ["href" => $path.$content['id'], "type" => 'GET'];
        return $content;
    }

    public function setGetPhonesMetadata(array $phones, Paginator $phoneEntities, array $options): array
    {
        $phones['items'] = $this->setGetPhonesLinks($phones['items']);

        return $this->setPagination($phones, $phoneEntities, $options);
    }

    public function setGetPhonesLinks(array $phones): array
    {
        foreach ($phones as $key => $phone) {
            $phones[$key] = $this->setSelfLink('/api/phones/', $phone);
        }

        return $phones;
    }

    public function setPagination(array $items, Paginator $entities, array $options): array
    {
        $items['page']['size'] = $entities->getQuery()->getMaxResults();
        $items['page']['totalElements'] = $entities->count();
        $items['page']['totalPages'] = intval(ceil($entities->count() / $entities->getQuery()->getMaxResults()));
        $items['page']['number'] = $options['page'];

        $items['_links']['first']['href'] = $options['baseLink'].'?page=1';

        if (1 < $options['page']) {
            $items['_links']['previous']['href']  = $options['baseLink'] . ('?page=' . ($options['page'] - 1));
        }

        $items['_links']['self']['href']  = $options['baseLink'].'?page='.$items['page']['number'];

        if ($items['page']['totalPages'] > $options['page']) {
            $items['_links']['next']['href']  = $options['baseLink'] . ('?page=' . ($options['page'] + 1));
        }

        $items['_links']['last']['href']  = $options['baseLink'].'?page='.$items['page']['totalPages'];


        return $items;
    }

    public function setUserListLinks(array $users, Paginator $entities, array $options): array
    {
        foreach ($users['items'] as $key => $user) {
            $users['items'][$key] = $this->setSelfLink('/api/me/users/', $user);
        }

        return $this->setPagination($users, $entities, $options);
    }
}