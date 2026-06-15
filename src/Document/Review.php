<?php

declare(strict_types=1);

namespace App\Document;

use App\Repositories\ReviewRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'reviews', repositoryClass: ReviewRepository::class)]
class Review
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $reviewerName;

    #[ODM\Field(type: 'float')]
    private float $rating;

    #[ODM\Field(type: 'string')]
    private string $content;

    #[ODM\Field(type: 'string')]
    private string $publishedAt;

    // Owning side — stored in Review document
    #[ODM\ReferenceOne(targetDocument: Album::class, inversedBy: 'reviews')]
    private ?Album $album = null;

    public function __construct(
        string $reviewerName,
        float $rating,
        string $content,
        string $publishedAt
    ) {
        $this->reviewerName = $reviewerName;
        $this->rating = $rating;
        $this->content = $content;
        $this->publishedAt = $publishedAt;
    }

    public function getId(): ?string { return $this->id; }
    public function getReviewerName(): string { return $this->reviewerName; }
    public function getRating(): float { return $this->rating; }
    public function getContent(): string { return $this->content; }
    public function getPublishedAt(): string { return $this->publishedAt; }
    public function getAlbum(): ?Album { return $this->album; }

    public function setAlbum(?Album $album): void { $this->album = $album; }
}
