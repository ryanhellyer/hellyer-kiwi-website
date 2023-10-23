<?php

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use Storage\Storage;
use View\View;

use Utils\Escaper;
use Utils\Files;
use Utils\Validation;
use Utils\FileHandler;

$storage = new Storage(
	new Escaper(),
	new Files(
		new Validation(),
        new FileHandler()
	),
	new Validation()
);

if (isset($_GET['save'])) {
    header('Content-Type: application/json');
    echo json_encode($storage->handleSaveRequest($_POST));
    exit;
}

if (isset($_GET['delete'])) {
    header('Content-Type: application/json');
    echo json_encode($storage->handleDeleteRequest($_POST));
    exit;
}

$view = new View(
    new Escaper(),
	new Files(
		new Validation(),
        new FileHandler()
	),
);
echo $view->displayMainTemplate();
die;
