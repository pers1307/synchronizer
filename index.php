<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once 'src/Synchronizer.php';

$localDb = [
    'host' => 'localhost',
    'name' => 'melcom',
    'user' => 'melcom',
    'pass' => 'dententyphi3'
];

$externalDb = [
    0 => [
        'host' => 'localhost',
        'name' => 'krasn',
        'user' => 'krasn',
        'pass' => '6sJoPLs6kXJogbMe'
    ]
];

$pdo = new Duras\Synchronizer\Synchronizer($externalDb, $localDb);

$pdo->sync();