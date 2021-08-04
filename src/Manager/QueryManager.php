<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\Phone;
use Doctrine\ORM\EntityManagerInterface;

class QueryManager
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function setGetPhonesData()
    {
        return $this->em->getRepository(Phone::class)->findPaginatedPhones();
    }

    public function setGetClientsData()
    {
        return $this->em->getRepository(Client::class)->findAll();
    }

    public function setGetUsersData(Client $client)
    {
        return $client->getUsers();
    }

    public function addUser()
    {
        $this->em->persist();
    }
}