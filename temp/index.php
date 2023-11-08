<?php

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use Storage\Storage;
use Utils\Escaper;
use Utils\Files;
use Utils\Validation;
use Utils\FileHandler;
use Utils\LoadJson;


if (isset($_GET['data'])) {
    $loadJSON = new LoadJson(
        new Files(
            new Validation(),
            new FileHandler()
        )
    );
    header('Content-Type: application/json');
    echo json_encode($loadJSON->getData(), JSON_PRETTY_PRINT);
    die;
}

if (isset($_GET['save'])) {
    $storage = new Storage(
        new Escaper(),
        new Files(
            new Validation(),
            new FileHandler()
        ),
        new Validation()
    );

    header('Content-Type: application/json');
    echo json_encode($storage->handleSaveRequest($_POST));
    exit;
}

if (isset($_GET['delete'])) {
    $storage = new Storage(
        new Escaper(),
        new Files(
            new Validation(),
            new FileHandler()
        ),
        new Validation()
    );

    header('Content-Type: application/json');
    echo json_encode($storage->handleDeleteRequest($_POST));
    exit;
}

echo file_get_contents( 'templates/main-template.tmpl');
die;
