<?php

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
*/
$error = 'Deprecated: Creation of dynamic property Utils\Files::$fileHandler is deprecated in /var/www/pressabl/public_html/temp/src/Utils/Files.php on line 30

Fatal error: Method View\View::__construct() cannot declare a return type in /var/www/pressabl/public_html/temp/src/View/View.php on line 27';

//echo 'Do the interfaces here make sense? index.php is the executing file:';
echo 'Write PHP unit tests for these. index.php is the executing file:';

echo "\n\n";
//echo $error;
echo "\n\n";


$files = [
    'src/Config/Config.php',
    'src/Interfaces/EscaperInterface.php',
    'src/Interfaces/FileHandlerInterface.php',
    'src/Interfaces/FilesInterface.php',
    'src/Interfaces/ValidationInterface.php',
    'src/Storage/Storage.php',
    'src/Utils/Escaper.php',
    'src/Utils/FileHandler.php',
    'src/Utils/Files.php',
    'src/Utils/Validation.php',
    'src/View/View.php',
    'index.php',
    'composer.json',
];
foreach ($files as $file) {
    echo "\n\n\n";
    echo $file . ":\n";
    $code = file_get_contents($file);
    $code = removePhpComments($code);
    echo $code;
}
die;

function removePhpComments(string $code): string {
    $tokens = token_get_all($code);
    $output = '';

    foreach ($tokens as $token) {
        if (is_array($token)) {
            list($id, $text) = $token;

            if ($id !== T_COMMENT && $id !== T_DOC_COMMENT) {
                $output .= $text;
            }
        } else {
            $output .= $token;
        }
    }

    return $output;
}