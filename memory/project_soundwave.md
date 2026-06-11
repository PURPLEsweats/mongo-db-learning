---
name: project-soundwave-records
description: MongoDB ODM demo project — SoundWave Records music label management system with 10 documents and vis.js relationship graph
metadata:
  type: project
---

SoundWave Records is a PHP 8.4 + Doctrine MongoDB ODM demo project in this repo. It models a music label business domain.

**10 Documents:** Artist (hub, 5 relationships), Album, Track, Genre, Label, Contract, Producer, Venue, Concert, Review.

**Artist relationships:** label (ReferenceOne→Label), albums (ReferenceMany←Album), genres (ReferenceMany→Genre), contracts (ReferenceMany←Contract), concerts (ReferenceMany←Concert).

**Stack:** PHP 8.4, doctrine/mongodb-odm ^2.6, mongodb/mongodb ^1.17, ext-mongodb 1.21.0, MongoDB 7, Docker.

**Why ext-mongodb 1.21.0 is pinned:** PECL's default now installs 2.x, which is incompatible with mongodb/mongodb ^1.x. The Dockerfile pins `pecl install mongodb-1.21.0`.

**How to apply:** If upgrading the MongoDB stack, update both `composer.json` (`mongodb/mongodb ^2.0`, `doctrine/mongodb-odm ^2.9`) AND remove the pin from Dockerfile.

**Running:** `make up` → `make seed` → open http://localhost:8080
**Re-seed:** `make seed` drops and re-creates all data.
**Frontend:** `public/index.php` — vis.js network graph, click nodes to see details.
