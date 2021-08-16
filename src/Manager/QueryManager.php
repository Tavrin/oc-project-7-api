<?php

namespace App\Manager;

use App\Entity\Phone;
use App\Entity\User;
use App\Http\ApiResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class QueryManager
{
    private EntityManagerInterface $em;
    private NormalizerInterface $normalizer;

    public function __construct(EntityManagerInterface $em, NormalizerInterface $normalizer)
    {
        $this->em = $em;
        $this->normalizer = $normalizer;
    }

    public function setGetPhonesData(array $queries)
    {
        return $this->em->getRepository(Phone::class)->findPaginatedPhones($queries['page'], $queries['limit'], $queries['sort']);
    }

    public function setGetUsersData(UserInterface $client, array $queries)
    {
        return $this->em->getRepository(User::class)->findClientUsers($client->getId(), $queries['page'], $queries['limit'], $queries['sort']);
    }

    public function addUser(array $data, $client)
    {
        if ($user = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email'], 'client' => $client])) {
            return new ApiResponse('user already exists for this client', $this->normalizer->normalize($user, 'json', ['groups' =>'user_show']), [], 303);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setClient($client);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function deleteUser(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}