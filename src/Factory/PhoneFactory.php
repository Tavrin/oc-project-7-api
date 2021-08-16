<?php

namespace App\Factory;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static      Phone|Proxy createOne(array $attributes = [])
 * @method static      Phone[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static      Phone|Proxy find($criteria)
 * @method static      Phone|Proxy findOrCreate(array $attributes)
 * @method static      Phone|Proxy first(string $sortedField = 'id')
 * @method static      Phone|Proxy last(string $sortedField = 'id')
 * @method static      Phone|Proxy random(array $attributes = [])
 * @method static      Phone|Proxy randomOrCreate(array $attributes = [])
 * @method static      Phone[]|Proxy[] all()
 * @method static      Phone[]|Proxy[] findBy(array $attributes)
 * @method static      Phone[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static      Phone[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static      PhoneRepository|RepositoryProxy repository()
 * @method Phone|Proxy create($attributes = [])
 */
final class PhoneFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->text(),
            'status' => true,
            'price' => self::faker()->randomNumber(3),
            'description' => self::faker()->paragraphs(
                self::faker()->numberBetween(1, 5),
                true
            ),
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Phone $phone) {})
        ;
    }

    protected static function getClass(): string
    {
        return Phone::class;
    }
}
