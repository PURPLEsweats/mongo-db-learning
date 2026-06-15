<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Document\Producer;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<Producer>
 */
class ProducerRepository extends DocumentRepository
{
    public function getByName(string $name): ?Producer
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * `specialties` is an array field — Mongo matches the document when the
     * value is present anywhere in that array.
     *
     * @return Producer[]
     */
    public function getBySpecialty(string $specialty): array
    {
        return $this->createQueryBuilder()
            ->field('specialties')->equals($specialty)
            ->sort('name', 'ASC')
            ->getQuery()
            ->toArray();
    }
}
