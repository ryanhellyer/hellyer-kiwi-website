<?php

declare(strict_types=1);

/*
*/
$error = 'Fatal error: Uncaught Error: Interface "Utils\ViewInterface" not found in /var/www/pressabl/public_html/temp/src/Utils/View.php:12 Stack trace: #0 /var/www/pressabl/public_html/temp/vendor/composer/ClassLoader.php(582): include() #1 /var/www/pressabl/public_html/temp/vendor/composer/ClassLoader.php(433): Composer\Autoload\{closure}() #2 /var/www/pressabl/public_html/temp/index.php(23): Composer\Autoload\ClassLoader->loadClass() #3 {main} thrown in /var/www/pressabl/public_html/temp/src/Utils/View.php on line 12';

echo 'The following files, are triggering this error, why?';
echo "\n\n";
echo $error;
echo "\n\n";


$files = [
    'src/Encrypted/Storage.php',
    /*
    'src/Utils/Escaper.php',
    'src/Interfaces/EscaperInterface.php',
    'src/Utils/Validation.php',
    'src/Interfaces/ValidationInterface.php',
    'src/Utils/Files.php',
    'src/Interfaces/FilesInterface.php',
    'src/Config/Config.php',
    */
    'src/Utils/View.php',
    'src/Interfaces/ViewInterface.php',
    'index.php',
    'composer.json',
];
foreach ($files as $file) {
    echo $file . ":\n";
    echo file_get_contents($file);
}
die;
