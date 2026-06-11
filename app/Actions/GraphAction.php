<?php

declare(strict_types=1);

namespace App\Actions;

use App\Document\Album;
use App\Document\Artist;
use App\Document\Concert;
use App\Document\Contract;
use App\Document\Genre;
use App\Document\Label;
use App\Document\Producer;
use App\Document\Review;
use App\Document\Track;
use App\Document\Venue;
use Doctrine\ODM\MongoDB\DocumentManager;
use Illuminate\Http\Response;

class GraphAction
{
    private array $typeColors = [
        'Artist'   => '#FF6B6B',
        'Album'    => '#4ECDC4',
        'Track'    => '#45B7D1',
        'Genre'    => '#96CEB4',
        'Label'    => '#FFEAA7',
        'Contract' => '#DDA0DD',
        'Producer' => '#98D8C8',
        'Venue'    => '#F7DC6F',
        'Concert'  => '#BB8FCE',
        'Review'   => '#F1948A',
    ];

    private array $typeShapes = [
        'Artist'   => 'star',
        'Album'    => 'box',
        'Track'    => 'ellipse',
        'Genre'    => 'diamond',
        'Label'    => 'hexagon',
        'Contract' => 'triangle',
        'Producer' => 'dot',
        'Venue'    => 'square',
        'Concert'  => 'triangleDown',
        'Review'   => 'ellipse',
    ];

    private array $nodes   = [];
    private array $edges   = [];
    private array $details = [];

    public function __construct(private readonly DocumentManager $dm) {}

    public function handle(): Response
    {
        $artists   = $this->dm->getRepository(Artist::class)->findAll();
        $albums    = $this->dm->getRepository(Album::class)->findAll();
        $tracks    = $this->dm->getRepository(Track::class)->findAll();
        $genres    = $this->dm->getRepository(Genre::class)->findAll();
        $labels    = $this->dm->getRepository(Label::class)->findAll();
        $contracts = $this->dm->getRepository(Contract::class)->findAll();
        $producers = $this->dm->getRepository(Producer::class)->findAll();
        $venues    = $this->dm->getRepository(Venue::class)->findAll();
        $concerts  = $this->dm->getRepository(Concert::class)->findAll();
        $reviews   = $this->dm->getRepository(Review::class)->findAll();

        foreach ($genres as $g) {
            $this->addNode('genre_' . $g->getId(), 'Genre', $g->getName(), [
                'Description' => $g->getDescription(),
            ]);
        }

        foreach ($labels as $l) {
            $this->addNode('label_' . $l->getId(), 'Label', $l->getName(), [
                'Founded' => $l->getFoundedYear(),
                'Country' => $l->getCountry(),
            ]);
        }

        foreach ($producers as $p) {
            $this->addNode('producer_' . $p->getId(), 'Producer', $p->getName(), [
                'Specialties' => implode(', ', $p->getSpecialties()),
            ]);
        }

        foreach ($venues as $v) {
            $this->addNode('venue_' . $v->getId(), 'Venue', $v->getName(), [
                'City'     => $v->getCity(),
                'Country'  => $v->getCountry(),
                'Capacity' => number_format($v->getCapacity()),
            ]);
        }

        foreach ($artists as $a) {
            $this->addNode('artist_' . $a->getId(), 'Artist', $a->getName(), [
                'Country'   => $a->getCountry(),
                'Formed'    => $a->getFormedYear(),
                'Bio'       => $a->getBio(),
                'Label'     => $a->getLabel()?->getName() ?? '—',
                'Genres'    => implode(', ', $a->getGenres()->map(fn($g) => $g->getName())->toArray()),
                'Albums'    => $a->getAlbums()->count(),
                'Contracts' => $a->getContracts()->count(),
                'Concerts'  => $a->getConcerts()->count(),
            ]);

            if ($a->getLabel()) {
                $this->addEdge('artist_' . $a->getId(), 'label_' . $a->getLabel()->getId(), 'signed to');
            }
            foreach ($a->getGenres() as $g) {
                $this->addEdge('artist_' . $a->getId(), 'genre_' . $g->getId(), 'genre');
            }
        }

        foreach ($albums as $al) {
            $this->addNode('album_' . $al->getId(), 'Album', $al->getTitle(), [
                'Released' => $al->getReleaseDate(),
                'Artist'   => $al->getArtist()?->getName() ?? '—',
                'Genre'    => $al->getGenre()?->getName() ?? '—',
                'Producer' => $al->getProducer()?->getName() ?? '—',
                'Tracks'   => $al->getTracks()->count(),
                'Reviews'  => $al->getReviews()->count(),
            ]);

            if ($al->getArtist()) {
                $this->addEdge('album_' . $al->getId(), 'artist_' . $al->getArtist()->getId(), 'by artist');
            }
            if ($al->getGenre()) {
                $this->addEdge('album_' . $al->getId(), 'genre_' . $al->getGenre()->getId(), 'genre');
            }
            if ($al->getProducer()) {
                $this->addEdge('album_' . $al->getId(), 'producer_' . $al->getProducer()->getId(), 'produced by');
            }
        }

        foreach ($tracks as $t) {
            $this->addNode('track_' . $t->getId(), 'Track', '#' . $t->getTrackNumber() . ' ' . $t->getTitle(), [
                'Title'    => $t->getTitle(),
                'Duration' => $t->getDurationFormatted(),
                'Track #'  => $t->getTrackNumber(),
                'Album'    => $t->getAlbum()?->getTitle() ?? '—',
            ]);
            if ($t->getAlbum()) {
                $this->addEdge('track_' . $t->getId(), 'album_' . $t->getAlbum()->getId(), 'on album');
            }
        }

        foreach ($contracts as $c) {
            $this->addNode('contract_' . $c->getId(), 'Contract', 'Contract', [
                'Artist'           => $c->getArtist()?->getName() ?? '—',
                'Label'            => $c->getLabel()?->getName() ?? '—',
                'Start'            => $c->getStartDate(),
                'End'              => $c->getEndDate(),
                'Advance'          => '$' . number_format($c->getAdvanceAmountUsd()),
                'Album Commitment' => $c->getAlbumCommitment() . ' albums',
            ]);
            if ($c->getArtist()) {
                $this->addEdge('contract_' . $c->getId(), 'artist_' . $c->getArtist()->getId(), 'for artist');
            }
            if ($c->getLabel()) {
                $this->addEdge('contract_' . $c->getId(), 'label_' . $c->getLabel()->getId(), 'with label');
            }
        }

        foreach ($concerts as $con) {
            $artist = $con->getArtist();
            $venue  = $con->getVenue();
            $this->addNode('concert_' . $con->getId(), 'Concert', $con->getDate(), [
                'Date'      => $con->getDate(),
                'Artist'    => $artist?->getName() ?? '—',
                'Venue'     => $venue?->getName() ?? '—',
                'City'      => $venue?->getCity() ?? '—',
                'Ticket'    => '$' . number_format($con->getTicketPriceUsd(), 2),
                'Attendees' => number_format($con->getAttendees()),
            ]);
            if ($artist) {
                $this->addEdge('concert_' . $con->getId(), 'artist_' . $artist->getId(), 'performed by');
            }
            if ($venue) {
                $this->addEdge('concert_' . $con->getId(), 'venue_' . $venue->getId(), 'at venue');
            }
        }

        foreach ($reviews as $r) {
            $this->addNode('review_' . $r->getId(), 'Review', $r->getReviewerName() . ' ' . $r->getRating() . '/10', [
                'Reviewer'  => $r->getReviewerName(),
                'Rating'    => $r->getRating() . ' / 10',
                'Album'     => $r->getAlbum()?->getTitle() ?? '—',
                'Published' => $r->getPublishedAt(),
                'Excerpt'   => substr($r->getContent(), 0, 120) . '...',
            ]);
            if ($r->getAlbum()) {
                $this->addEdge('review_' . $r->getId(), 'album_' . $r->getAlbum()->getId(), 'reviews');
            }
        }

        $data = [
            'nodesJson'   => json_encode($this->nodes, JSON_THROW_ON_ERROR),
            'edgesJson'   => json_encode($this->edges, JSON_THROW_ON_ERROR),
            'detailsJson' => json_encode($this->details, JSON_THROW_ON_ERROR),
            'typeColors'  => $this->typeColors,
            'counts'      => [
                'Artists'   => count($artists),
                'Albums'    => count($albums),
                'Tracks'    => count($tracks),
                'Genres'    => count($genres),
                'Labels'    => count($labels),
                'Contracts' => count($contracts),
                'Producers' => count($producers),
                'Venues'    => count($venues),
                'Concerts'  => count($concerts),
                'Reviews'   => count($reviews),
            ],
        ];

        return response($this->render('graph', $data));
    }

    private function addNode(string $id, string $type, string $label, array $detail): void
    {
        $this->nodes[] = [
            'id'    => $id,
            'label' => wordwrap($label, 12, "\n", true),
            'title' => $type,
            'group' => $type,
            'color' => ['background' => $this->typeColors[$type], 'border' => '#333'],
            'shape' => $this->typeShapes[$type],
            'font'  => ['size' => 12],
            'size'  => ($type === 'Artist') ? 40 : 20,
        ];
        $this->details[$id] = array_merge(['__type' => $type, '__label' => $label], $detail);
    }

    private function addEdge(string $from, string $to, string $label): void
    {
        if ($from && $to) {
            $this->edges[] = [
                'from'   => $from,
                'to'     => $to,
                'label'  => $label,
                'arrows' => 'to',
                'font'   => ['size' => 10, 'align' => 'middle'],
            ];
        }
    }

    private function render(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        include base_path("resources/views/{$view}.php");
        return ob_get_clean();
    }
}
