<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Document\Album;
use App\Document\Artist;
use App\Document\Genre;
use App\Document\Producer;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<Album>
 *
 * Inherits find(), findAll(), findBy(), findOneBy() from DocumentRepository.
 * The getX() methods below wrap the common queries for Albums.
 */
class AlbumRepository extends DocumentRepository
{
    /** @return Album[] */
    public function getByArtist(Artist $artist): array
    {
        return $this->findBy(['artist' => $artist], ['releaseYear' => 'ASC']);
    }

    /** @return Album[] */
    public function getByGenre(Genre $genre): array
    {
        return $this->findBy(['genre' => $genre], ['releaseYear' => 'ASC']);
    }

    /** @return Album[] */
    public function getByProducer(Producer $producer): array
    {
        return $this->findBy(['producer' => $producer], ['releaseYear' => 'ASC']);
    }

    /** @return Album[] */
    public function getByReleaseYear(int $year): array
    {
        return $this->findBy(['releaseYear' => $year], ['title' => 'ASC']);
    }

    /** @return Album[] */
    public function getReleasedBetween(int $fromYear, int $toYear): array
    {
        return $this->createQueryBuilder()
            ->field('releaseYear')->gte($fromYear)
            ->field('releaseYear')->lte($toYear)
            ->sort('releaseYear', 'ASC')
            ->getQuery()
            ->toArray();
    }

    /**
     * All albums released in a given decade, e.g. getReleasedInDecade(1990).
     *
     * @return Album[]
     */
    public function getReleasedInDecade(int $decadeStart): array
    {
        return $this->getReleasedBetween($decadeStart, $decadeStart + 9);
    }
}
