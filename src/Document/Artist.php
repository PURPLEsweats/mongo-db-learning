<?php

declare(strict_types=1);

namespace App\Document;

use App\Repositories\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Hub document — has 5 relationships:
 *   label, albums, genres, contracts, concerts
 */
#[ODM\Document(collection: 'artists', repositoryClass: ArtistRepository::class)]
class Artist
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $name;

    #[ODM\Field(type: 'string')]
    private string $bio;

    #[ODM\Field(type: 'string')]
    private string $country;

    #[ODM\Field(type: 'int')]
    private int $formedYear;

    // Relationship 1: signed to a Label (owning side — stored in Artist doc)
    #[ODM\ReferenceOne(targetDocument: Label::class, inversedBy: 'artists')]
    private ?Label $label = null;

    // Relationship 2: has many Albums (inverse — Album owns the reference)
    #[ODM\ReferenceMany(targetDocument: Album::class, mappedBy: 'artist')]
    private Collection $albums;

    // Relationship 3: tagged with Genres (owning side — stored as array of refs)
    #[ODM\ReferenceMany(targetDocument: Genre::class)]
    private Collection $genres;

    // Relationship 4: has many Contracts (inverse — Contract owns the reference)
    #[ODM\ReferenceMany(targetDocument: Contract::class, mappedBy: 'artist')]
    private Collection $contracts;

    // Relationship 5: played many Concerts (inverse — Concert owns the reference)
    #[ODM\ReferenceMany(targetDocument: Concert::class, mappedBy: 'artist')]
    private Collection $concerts;

    public function __construct(string $name, string $bio, string $country, int $formedYear)
    {
        $this->name = $name;
        $this->bio = $bio;
        $this->country = $country;
        $this->formedYear = $formedYear;
        $this->albums = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->contracts = new ArrayCollection();
        $this->concerts = new ArrayCollection();
    }

    public function getId(): ?string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getBio(): string { return $this->bio; }
    public function getCountry(): string { return $this->country; }
    public function getFormedYear(): int { return $this->formedYear; }
    public function getLabel(): ?Label { return $this->label; }
    public function getAlbums(): Collection { return $this->albums; }
    public function getGenres(): Collection { return $this->genres; }
    public function getContracts(): Collection { return $this->contracts; }
    public function getConcerts(): Collection { return $this->concerts; }

    public function setLabel(?Label $label): void { $this->label = $label; }
    public function addGenre(Genre $genre): void { $this->genres->add($genre); }
}
