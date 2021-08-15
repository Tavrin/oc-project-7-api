<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Enum\PhoneEnums;
use App\Factory\ClientFactory;
use App\Factory\PhoneFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach (PhoneEnums::PHONE as $phone) {
            PhoneFactory::new()->create(['name' => $phone['name'], 'price' => $phone['price']]);
        }

        ClientFactory::new()->createMany(10);
        UserFactory::new()->createMany(
            30,
            function() {
                return ['client' => ClientFactory::random()];
            }
        );

        $client = new Client();

        $client->setEmail('etienne.doux@gmail.com');
        $client->setPassword('$2y$13$t0cQrAU.xgfCzHex.Xc.Se4Y.gF9zNmki5GsngCoUj.GUz3Nk2iw.');
        $client->setName('Etienne');
        $client->setRoles(['ROLE_USER']);
        $manager->persist($client);
        $manager->flush();

        UserFactory::new()->createMany(
            10,
            ['client' => $client]
        );

        $manager->flush();
    }
}
