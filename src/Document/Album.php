<?php

declare(strict_types=1);

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'albums')]
class Album
{
    #[ODM\Id]
    private ?string $id = null;
    #[ODM\Field(type: 'string')]
    private string $title;
    #[ODM\Field(type: 'string')]
    private string $releaseDate;
    #[ODM\Field(type: 'int')]
    private int $releaseYear;
    // Owning side — stored in Album document
    #[ODM\ReferenceOne(targetDocument: Artist::class, inversedBy: 'albums')]
    private ?Artist $artist = null;

    #[ODM\ReferenceOne(targetDocument: Genre::class)]
    private ?Genre $genre = null;

    #[ODM\ReferenceOne(targetDocument: Producer::class, inversedBy: 'albums')]
    private ?Producer $producer = null;

    // Inverse side — Track owns the reference
    #[ODM\ReferenceMany(targetDocument: Track::class, mappedBy: 'album')]
    private Collection $tracks;

    // Inverse side — Review owns the reference
    #[ODM\ReferenceMany(targetDocument: Review::class, mappedBy: 'album')]
    private Collection $reviews;

    public function __construct(string $title, string $releaseDate, int $releaseYear)
    {
        $this->title = $title;
        $this->releaseDate = $releaseDate;
        $this->releaseYear = $releaseYear;
        $this->tracks = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?string { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getReleaseDate(): string { return $this->releaseDate; }
    public function getReleaseYear(): int { return $this->releaseYear; }
    public function getArtist(): ?Artist { return $this->artist; }
    public function getGenre(): ?Genre { return $this->genre; }
    public function getProducer(): ?Producer { return $this->producer; }
    public function getTracks(): Collection { return $this->tracks; }
    public function getReviews(): Collection { return $this->reviews; }

    public function setArtist(?Artist $artist): void { $this->artist = $artist; }
    public function setGenre(?Genre $genre): void { $this->genre = $genre; }
    public function setProducer(?Producer $producer): void { $this->producer = $producer; }
}
