<?php

declare(strict_types=1);

namespace App\Document;

use App\Repositories\ProducerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'producers', repositoryClass: ProducerRepository::class)]
class Producer
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $name;

    #[ODM\Field(type: 'collection')]
    private array $specialties = [];

    #[ODM\ReferenceMany(targetDocument: Album::class, mappedBy: 'producer')]
    private Collection $albums;

    public function __construct(string $name, array $specialties = [])
    {
        $this->name = $name;
        $this->specialties = $specialties;
        $this->albums = new ArrayCollection();
    }

    public function getId(): ?string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getSpecialties(): array { return $this->specialties; }
    public function getAlbums(): Collection { return $this->albums; }
}
