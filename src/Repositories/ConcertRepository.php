<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Document\Artist;
use App\Document\Concert;
use App\Document\Venue;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<Concert>
 */
class ConcertRepository extends DocumentRepository
{
    /** @return Concert[] */
    public function getByArtist(Artist $artist): array
    {
        return $this->findBy(['artist' => $artist], ['date' => 'ASC']);
    }

    /** @return Concert[] */
    public function getByVenue(Venue $venue): array
    {
        return $this->findBy(['venue' => $venue], ['date' => 'ASC']);
    }

    /** @return Concert[] */
    public function getWithAttendeesAtLeast(int $attendees): array
    {
        return $this->createQueryBuilder()
            ->field('attendees')->gte($attendees)
            ->sort('attendees', 'DESC')
            ->getQuery()
            ->toArray();
    }
}
