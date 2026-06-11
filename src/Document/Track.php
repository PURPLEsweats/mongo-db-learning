<?php

declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'tracks')]
class Track
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $title;

    #[ODM\Field(type: 'int')]
    private int $durationSeconds;

    #[ODM\Field(type: 'int')]
    private int $trackNumber;

    // Owning side — stored in Track document
    #[ODM\ReferenceOne(targetDocument: Album::class, inversedBy: 'tracks')]
    private ?Album $album = null;

    public function __construct(string $title, int $durationSeconds, int $trackNumber)
    {
        $this->title = $title;
        $this->durationSeconds = $durationSeconds;
        $this->trackNumber = $trackNumber;
    }

    public function getId(): ?string { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getDurationSeconds(): int { return $this->durationSeconds; }
    public function getTrackNumber(): int { return $this->trackNumber; }
    public function getAlbum(): ?Album { return $this->album; }

    public function getDurationFormatted(): string
    {
        return sprintf('%d:%02d', intdiv($this->durationSeconds, 60), $this->durationSeconds % 60);
    }

    public function setAlbum(?Album $album): void { $this->album = $album; }
}
