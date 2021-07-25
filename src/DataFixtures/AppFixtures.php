<?php

namespace App\DataFixtures;

use App\Enum\PhoneNameEnums;
use App\Factory\ClientFactory;
use App\Factory\PhoneFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach (PhoneNameEnums::PHONE_NAME as $phone) {
            PhoneFactory::new()->create(['name' => $phone]);
        }

        ClientFactory::new()->createMany(10);
        UserFactory::new()->createMany(
            30,
            function() {
                return ['client' => ClientFactory::random()];
            }
        );

        $manager->flush();
    }
}
