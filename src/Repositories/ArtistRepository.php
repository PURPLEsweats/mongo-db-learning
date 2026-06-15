<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Document\Artist;
use App\Document\Genre;
use App\Document\Label;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<Artist>
 */
class ArtistRepository extends DocumentRepository
{
    public function getByName(string $name): ?Artist
    {
        return $this->findOneBy(['name' => $name]);
    }

    /** @return Artist[] */
    public function getByCountry(string $country: array
    {
        return $this->findBy(['country' => $country], ['name' => 'ASC']);
    }

    /** @return Artist[] */
    public function getByLabel(Label $label): array
    {
        return $this->findBy(['label' => $label], ['name' => 'ASC']);
    }

    /**
     * Genres are an owning ReferenceMany on Artist (stored as an array of
     * refs), so we match the Genre object against that array.
     *
     * @return Artist[]
     */
    public function getByGenre(Genre $genre): array
    {
        return $this->createQueryBuilder()
            ->field('genres')->references($genre)
            ->sort('name', 'ASC')
            ->getQuery()
            ->toArray();
    }

    /** @return Artist[] */
    public function getFormedAfter(int $year): array
    {
        return $this->createQueryBuilder()
            ->field('formedYear')->gte($year)
            ->sort('formedYear', 'ASC')
            ->getQuery()
            ->toArray();
    }
}
