<?php

declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'concerts')]
class Concert
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $date;

    #[ODM\Field(type: 'float')]
    private float $ticketPriceUsd;

    #[ODM\Field(type: 'int')]
    private int $attendees;

    // Owning side — stored in Concert document
    #[ODM\ReferenceOne(targetDocument: Artist::class, inversedBy: 'concerts')]
    private ?Artist $artist = null;

    #[ODM\ReferenceOne(targetDocument: Venue::class)]
    private ?Venue $venue = null;

    public function __construct(string $date, float $ticketPriceUsd, int $attendees)
    {
        $this->date = $date;
        $this->ticketPriceUsd = $ticketPriceUsd;
        $this->attendees = $attendees;
    }

    public function getId(): ?string { return $this->id; }
    public function getDate(): string { return $this->date; }
    public function getTicketPriceUsd(): float { return $this->ticketPriceUsd; }
    public function getAttendees(): int { return $this->attendees; }
    public function getArtist(): ?Artist { return $this->artist; }
    public function getVenue(): ?Venue { return $this->venue; }

    public function setArtist(?Artist $artist): void { $this->artist = $artist; }
    public function setVenue(?Venue $venue): void { $this->venue = $venue; }
}
