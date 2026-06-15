<?php

declare(strict_types=1);

namespace App\Document;

use App\Repositories\ContractRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'contracts', repositoryClass: ContractRepository::class)]
class Contract
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $startDate;

    #[ODM\Field(type: 'string')]
    private string $endDate;

    #[ODM\Field(type: 'int')]
    private int $advanceAmountUsd;

    #[ODM\Field(type: 'int')]
    private int $albumCommitment;

    // Owning side — stored in Contract document
    #[ODM\ReferenceOne(targetDocument: Artist::class, inversedBy: 'contracts')]
    private ?Artist $artist = null;

    #[ODM\ReferenceOne(targetDocument: Label::class)]
    private ?Label $label = null;

    public function __construct(
        string $startDate,
        string $endDate,
        int $advanceAmountUsd,
        int $albumCommitment
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->advanceAmountUsd = $advanceAmountUsd;
        $this->albumCommitment = $albumCommitment;
    }

    public function getId(): ?string { return $this->id; }
    public function getStartDate(): string { return $this->startDate; }
    public function getEndDate(): string { return $this->endDate; }
    public function getAdvanceAmountUsd(): int { return $this->advanceAmountUsd; }
    public function getAlbumCommitment(): int { return $this->albumCommitment; }
    public function getArtist(): ?Artist { return $this->artist; }
    public function getLabel(): ?Label { return $this->label; }

    public function setArtist(?Artist $artist): void { $this->artist = $artist; }
    public function setLabel(?Label $label): void { $this->label = $label; }
}
