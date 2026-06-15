<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Document\Album;
use App\Document\Track;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<Track>
 */
class TrackRepository extends DocumentRepository
{
    /**
     * Tracks on an album, in track-number order (the natural tracklist).
     *
     * @return Track[]
     */
    public function getByAlbum(Album $album): array
    {
        return $this->findBy(['album' => $album], ['trackNumber' => 'ASC']);
    }

    /** @return Track[] */
    public function getLongerThan(int $seconds): array
    {
        return $this->createQueryBuilder()
            ->field('durationSeconds')->gt($seconds)
            ->sort('durationSeconds', 'DESC')
            ->getQuery()
            ->toArray();
    }
}
