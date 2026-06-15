<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Document\Artist;
use App\Document\Contract;
use App\Document\Label;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<Contract>
 */
class ContractRepository extends DocumentRepository
{
    /** @return Contract[] */
    public function getByArtist(Artist $artist): array
    {
        return $this->findBy(['artist' => $artist], ['startDate' => 'ASC']);
    }

    /** @return Contract[] */
    public function getByLabel(Label $label): array
    {
        return $this->findBy(['label' => $label], ['startDate' => 'ASC']);
    }

    /** @return Contract[] */
    public function getWithAdvanceAtLeast(int $usd): array
    {
        return $this->createQueryBuilder()
            ->field('advanceAmountUsd')->gte($usd)
            ->sort('advanceAmountUsd', 'DESC')
            ->getQuery()
            ->toArray();
    }
}
