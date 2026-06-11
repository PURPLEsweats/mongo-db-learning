<?php

declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'venues')]
class Venue
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $name;

    #[ODM\Field(type: 'string')]
    private string $city;

    #[ODM\Field(type: 'string')]
    private string $country;

    #[ODM\Field(type: 'int')]
    private int $capacity;

    public function __construct(string $name, string $city, string $country, int $capacity)
    {
        $this->name = $name;
        $this->city = $city;
        $this->country = $country;
        $this->capacity = $capacity;
    }

    public function getId(): ?string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getCity(): string { return $this->city; }
    public function getCountry(): string { return $this->country; }
    public function getCapacity(): int { return $this->capacity; }
}
