<?php

declare(strict_types=1);

/** @var \Doctrine\ODM\MongoDB\DocumentManager $dm */
$dm = require __DIR__ . '/bootstrap/odm.php';

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

// --- Wipe existing data ---
$dm->getClient()->selectDatabase('soundwave_records')->drop();
echo "Dropped existing database.\n";

// --- Genres ---
$synthpop   = new Genre('Synthpop', 'Electronic music influenced by 80s synth aesthetics');
$dreamPop   = new Genre('Dream Pop', 'Ethereal, reverb-heavy pop with lush soundscapes');
$chillwave  = new Genre('Chillwave', 'Lo-fi, hazy electronic pop with nostalgic textures');
$electronic = new Genre('Electronic', 'Broad category of music produced with electronic instruments');
$indiePop   = new Genre('Indie Pop', 'Independent pop with alternative sensibilities');

$dm->persist($synthpop);
$dm->persist($dreamPop);
$dm->persist($chillwave);
$dm->persist($electronic);
$dm->persist($indiePop);
echo "Genres created.\n";

// --- Labels ---
$soundwave = new Label('SoundWave Records', 2010, 'United States');
$neonNights = new Label('Neon Nights Records', 2008, 'United Kingdom');

$dm->persist($soundwave);
$dm->persist($neonNights);
echo "Labels created.\n";

// --- Producers ---
$tylerLyle    = new Producer('Tyler Lyle', ['Synthpop', 'Mixing', 'Songwriting']);
$laurenMay    = new Producer('Lauren Mayberry', ['Electronic', 'Vocal Production', 'Arrangement']);
$reedKuhn     = new Producer('Reed Kuhn', ['Chillwave', 'Sampling', 'Lo-fi']);

$dm->persist($tylerLyle);
$dm->persist($laurenMay);
$dm->persist($reedKuhn);
echo "Producers created.\n";

// --- Venues ---
$fillmore  = new Venue('The Fillmore', 'San Francisco', 'United States', 1315);
$roundhouse = new Venue('Roundhouse', 'London', 'United Kingdom', 3300);
$bowery    = new Venue('Bowery Ballroom', 'New York City', 'United States', 575);

$dm->persist($fillmore);
$dm->persist($roundhouse);
$dm->persist($bowery);
echo "Venues created.\n";

// Flush so IDs are assigned before setting up cross-references
$dm->flush();

// --- Artists ---
$theMidnight = new Artist(
    'The Midnight',
    'Tyler Lyle and Tim McEwan crafting cinematic synthwave from Los Angeles. Known for nostalgic 80s aesthetics and driving drum machines.',
    'United States',
    2012
);
$theMidnight->setLabel($soundwave);
$theMidnight->addGenre($synthpop);
$theMidnight->addGenre($dreamPop);

$chvrches = new Artist(
    'CHVRCHES',
    'Scottish synth-pop trio from Glasgow led by Lauren Mayberry. Sharp electronic production meets hook-driven anthems.',
    'United Kingdom',
    2011
);
$chvrches->setLabel($neonNights);
$chvrches->addGenre($synthpop);
$chvrches->addGenre($indiePop);

$washedOut = new Artist(
    'Washed Out',
    'Ernest Greene\'s project that defined the chillwave movement. Hazy, blissful electronic pop drenched in reverb.',
    'United States',
    2009
);
$washedOut->setLabel($soundwave);
$washedOut->addGenre($chillwave);
$washedOut->addGenre($dreamPop);

$dm->persist($theMidnight);
$dm->persist($chvrches);
$dm->persist($washedOut);
echo "Artists created.\n";

$dm->flush();

// --- Albums ---
$monsters = new Album('Monsters', '2022-06-17', 2022);
$monsters->setArtist($theMidnight);
$monsters->setGenre($synthpop);
$monsters->setProducer($tylerLyle);

$heroes = new Album('Heroes', '2020-04-03', 2020);
$heroes->setArtist($theMidnight);
$heroes->setGenre($dreamPop);
$heroes->setProducer($tylerLyle);

$screenViolence = new Album('Screen Violence', '2021-08-27', 2021);
$screenViolence->setArtist($chvrches);
$screenViolence->setGenre($synthpop);
$screenViolence->setProducer($laurenMay);

$misterMellow = new Album('Mister Mellow', '2017-06-09', 2017);
$misterMellow->setArtist($washedOut);
$misterMellow->setGenre($chillwave);
$misterMellow->setProducer($reedKuhn);

$dm->persist($monsters);
$dm->persist($heroes);
$dm->persist($screenViolence);
$dm->persist($misterMellow);
echo "Albums created.\n";

$dm->flush();

// --- Tracks ---
$tracks = [
    [new Track('Monsters', 235, 1), $monsters],
    [new Track('Gloria', 260, 2), $monsters],
    [new Track('Avalanche', 310, 3), $monsters],
    [new Track('Heroes', 245, 1), $heroes],
    [new Track('Belong', 235, 2), $heroes],
    [new Track('He Said She Said', 215, 1), $screenViolence],
    [new Track('How Not to Drown', 228, 2), $screenViolence],
    [new Track('Jet Blues', 198, 1), $misterMellow],
    [new Track('Get Lost', 212, 2), $misterMellow],
];

foreach ($tracks as [$track, $album]) {
    /** @var Track $track */
    /** @var Album $album */
    $track->setAlbum($album);
    $dm->persist($track);
}
echo "Tracks created.\n";

// --- Contracts ---
$contract1 = new Contract('2018-01-01', '2024-12-31', 500_000, 3);
$contract1->setArtist($theMidnight);
$contract1->setLabel($soundwave);

$contract2 = new Contract('2019-06-01', '2025-05-31', 350_000, 2);
$contract2->setArtist($washedOut);
$contract2->setLabel($soundwave);

$contract3 = new Contract('2015-03-01', '2026-02-28', 600_000, 4);
$contract3->setArtist($chvrches);
$contract3->setLabel($neonNights);

$dm->persist($contract1);
$dm->persist($contract2);
$dm->persist($contract3);
echo "Contracts created.\n";

// --- Concerts ---
$concert1 = new Concert('2023-03-15', 45.00, 1200);
$concert1->setArtist($theMidnight);
$concert1->setVenue($fillmore);

$concert2 = new Concert('2023-09-22', 55.00, 3100);
$concert2->setArtist($chvrches);
$concert2->setVenue($roundhouse);

$concert3 = new Concert('2022-11-10', 35.00, 520);
$concert3->setArtist($washedOut);
$concert3->setVenue($bowery);

$concert4 = new Concert('2024-02-28', 50.00, 1250);
$concert4->setArtist($theMidnight);
$concert4->setVenue($bowery);

$dm->persist($concert1);
$dm->persist($concert2);
$dm->persist($concert3);
$dm->persist($concert4);
echo "Concerts created.\n";

// --- Reviews ---
$review1 = new Review('Pitchfork', 8.2, 'A lush, emotionally resonant synthpop record that cements The Midnight\'s place as the genre\'s defining act.', '2022-06-20');
$review1->setAlbum($monsters);

$review2 = new Review('NME', 9.0, 'CHVRCHES return with their most mature and haunting work yet — Screen Violence is a career highlight.', '2021-09-01');
$review2->setAlbum($screenViolence);

$review3 = new Review('The Guardian', 7.5, 'Washed Out keeps the chillwave flame burning with hazy, sun-soaked grooves on Mister Mellow.', '2017-06-15');
$review3->setAlbum($misterMellow);

$review4 = new Review('Consequence of Sound', 8.5, 'Heroes is a triumph of melodic synthesis and lyrical depth from The Midnight.', '2020-04-10');
$review4->setAlbum($heroes);

$dm->persist($review1);
$dm->persist($review2);
$dm->persist($review3);
$dm->persist($review4);
echo "Reviews created.\n";

$dm->flush();
echo "\nSeeding complete! SoundWave Records database is ready.\n";
