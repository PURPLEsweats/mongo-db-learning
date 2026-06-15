<?php

declare(strict_types=1);

namespace App\Actions;

use App\Document\Album;
use App\Document\Artist;
use Doctrine\ODM\MongoDB\DocumentManager;
use Illuminate\Http\Response;

class TestingAction
{
    public function __construct(private readonly DocumentManager $dm) {}

    public function handle(): Response
    {
        // Mess around here — $this->dm has the full DocumentManager
        $out = [];

        // ─────────────────────────────────────────────────────────────
        // getRepository() now returns your custom repositories, so the
        // getX() methods are available with full type hints.
        //   Album::class  → App\Repositories\AlbumRepository
        //   Artist::class → App\Repositories\ArtistRepository
        // ─────────────────────────────────────────────────────────────
        $albumRepo  = $this->dm->getRepository(Album::class);
        $artistRepo = $this->dm->getRepository(Artist::class);

        // Inherited from DocumentRepository — still there for free.
        $allArtists = $artistRepo->findAll();
        $firstArtist = $allArtists[0] ?? null;

        if ($firstArtist === null) {
            return response('<p>No artists in the DB yet — run the seeder first.</p>');
        }

        // ─────────────────────────────────────────────────────────────
        // ★ "All albums by X artist" — now a named method
        // ─────────────────────────────────────────────────────────────
        $out[] = '<h2>Albums by ' . e($firstArtist->getName()) . '</h2>';
        foreach ($albumRepo->getByArtist($firstArtist) as $album) {
            $out[] = '<p>' . e($album->getTitle()) . ' (' . $album->getReleaseYear() . ')</p>';
        }

        // ─────────────────────────────────────────────────────────────
        // Range query — albums released in the 1990s
        // ─────────────────────────────────────────────────────────────
        $out[] = '<h2>Albums released in the 1990s</h2>';
        foreach ($albumRepo->getReleasedInDecade(1990) as $album) {
            $out[] = sprintf(
                '<p>%d — %s — %s</p>',
                $album->getReleaseYear(),
                e($album->getTitle()),
                e($album->getArtist()?->getName() ?? '—'),
            );
        }

        // ─────────────────────────────────────────────────────────────
        // Artist lookups
        // ─────────────────────────────────────────────────────────────
        $out[] = '<h2>Artists from ' . e($firstArtist->getCountry()) . '</h2>';
        foreach ($artistRepo->getByCountry($firstArtist->getCountry()) as $artist) {
            $out[] = '<p>' . e($artist->getName()) . ' (formed ' . $artist->getFormedYear() . ')</p>';
        }

        // ─────────────────────────────────────────────────────────────
        // Walk relationships off the document instead of querying
        // ─────────────────────────────────────────────────────────────
        if ($firstArtist->getLabel() !== null) {
            $out[] = '<h2>Label-mates of ' . e($firstArtist->getName()) . '</h2>';
            // Artist → Label → its artists (the inverse ReferenceMany)
            foreach ($firstArtist->getLabel()->getArtists() as $mate) {
                $out[] = '<p>' . e($mate->getName()) . '</p>';
            }
        }

        return response(implode("\n", $out) ?: '<p>Nothing to show yet.</p>');
    }
}
