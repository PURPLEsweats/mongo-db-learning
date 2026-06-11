<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AttributeDriver;
use MongoDB\Client;

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://localhost:27017');

$config = new Configuration();
$config->setProxyDir(dirname(__DIR__) . '/var/proxies');
$config->setProxyNamespace('Proxies');
$config->setHydratorDir(dirname(__DIR__) . '/var/hydrators');
$config->setHydratorNamespace('Hydrators');
$config->setDefaultDB('soundwave_records');
$config->setMetadataDriverImpl(
    new AttributeDriver([dirname(__DIR__) . '/src/Document'])
);

return DocumentManager::create($client, $config);
