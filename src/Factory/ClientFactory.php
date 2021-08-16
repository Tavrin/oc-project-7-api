<?php

namespace App\Factory;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static       Client|Proxy createOne(array $attributes = [])
 * @method static       Client[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static       Client|Proxy find($criteria)
 * @method static       Client|Proxy findOrCreate(array $attributes)
 * @method static       Client|Proxy first(string $sortedField = 'id')
 * @method static       Client|Proxy last(string $sortedField = 'id')
 * @method static       Client|Proxy random(array $attributes = [])
 * @method static       Client|Proxy randomOrCreate(array $attributes = [])
 * @method static       Client[]|Proxy[] all()
 * @method static       Client[]|Proxy[] findBy(array $attributes)
 * @method static       Client[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static       Client[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static       ClientRepository|RepositoryProxy repository()
 * @method Client|Proxy create($attributes = [])
 */
final class ClientFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://github.com/zenstruck/foundry#model-factories)
            'email' => self::faker()->email(),
            'roles' => ['ROLE_USER'],
            'description' => self::faker()->paragraphs(
                self::faker()->numberBetween(1, 5),
                true
            ),
            'password' => self::faker()->password(),
            'name' => self::faker()->company(),
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Client $client) {})
        ;
    }

    protected static function getClass(): string
    {
        return Client::class;
    }
}
