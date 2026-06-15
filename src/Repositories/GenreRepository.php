<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Document\Genre;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<Genre>
 */
class GenreRepository extends DocumentRepository
{
    public function getByName(string $name): ?Genre
    {
        return $this->findOneBy(['name' => $name]);
    }

    /** @return Genre[] */
    public function getAllOrderedByName(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }
}
