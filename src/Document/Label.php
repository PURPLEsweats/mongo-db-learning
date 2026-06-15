<?php

declare(strict_types=1);

namespace App\Document;

use App\Repositories\LabelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'labels', repositoryClass: LabelRepository::class)]
class Label
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $name;

    #[ODM\Field(type: 'int')]
    private int $foundedYear;

    #[ODM\Field(type: 'string')]
    private string $country;

    #[ODM\ReferenceMany(targetDocument: Artist::class, mappedBy: 'label')]
    private Collection $artists;

    public function __construct(string $name, int $foundedYear, string $country)
    {
        $this->name = $name;
        $this->foundedYear = $foundedYear;
        $this->country = $country;
        $this->artists = new ArrayCollection();
    }

    public function getId(): ?string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getFoundedYear(): int { return $this->foundedYear; }
    public function getCountry(): string { return $this->country; }
    public function getArtists(): Collection { return $this->artists; }
}
