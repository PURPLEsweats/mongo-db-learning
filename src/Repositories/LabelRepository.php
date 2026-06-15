<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Document\Label;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<Label>
 */
class LabelRepository extends DocumentRepository
{
    public function getByName(string $name): ?Label
    {
        return $this->findOneBy(['name' => $name]);
    }

    /** @return Label[] */
    public function getByCountry(string $country): array
    {
        return $this->findBy(['country' => $country], ['name' => 'ASC']);
    }

    /** @return Label[] */
    public function getFoundedBefore(int $year): array
    {
        return $this->createQueryBuilder()
            ->field('foundedYear')->lt($year)
            ->sort('foundedYear', 'ASC')
            ->getQuery()
            ->toArray();
    }
}
