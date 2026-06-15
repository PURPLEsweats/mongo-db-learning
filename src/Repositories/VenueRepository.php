<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Document\Venue;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<Venue>
 */
class VenueRepository extends DocumentRepository
{
    /** @return Venue[] */
    public function getByCity(string $city): array
    {
        return $this->findBy(['city' => $city], ['name' => 'ASC']);
    }

    /** @return Venue[] */
    public function getByCountry(string $country): array
    {
        return $this->findBy(['country' => $country], ['name' => 'ASC']);
    }

    /** @return Venue[] */
    public function getWithCapacityAtLeast(int $capacity): array
    {
        return $this->createQueryBuilder()
            ->field('capacity')->gte($capacity)
            ->sort('capacity', 'DESC')
            ->getQuery()
            ->toArray();
    }
}
