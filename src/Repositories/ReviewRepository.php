<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Document\Album;
use App\Document\Review;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<Review>
 */
class ReviewRepository extends DocumentRepository
{
    /**
     * Reviews for an album, highest rating first.
     *
     * @return Review[]
     */
    public function getByAlbum(Album $album): array
    {
        return $this->findBy(['album' => $album], ['rating' => 'DESC']);
    }

    /** @return Review[] */
    public function getByReviewer(string $reviewerName): array
    {
        return $this->findBy(['reviewerName' => $reviewerName], ['publishedAt' => 'DESC']);
    }

    /** @return Review[] */
    public function getWithRatingAtLeast(float $rating): array
    {
        return $this->createQueryBuilder()
            ->field('rating')->gte($rating)
            ->sort('rating', 'DESC')
            ->getQuery()
            ->toArray();
    }
}
