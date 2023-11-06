<?php

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
*/
$error = '
<br />
<b>Fatal error</b>:  Uncaught TypeError: Utils\LoadJson::getData(): Return value must be of type string, array returned in /var/www/pressabl/public_html/temp/php/Utils/LoadJson.php:47
Stack trace:
#0 /var/www/pressabl/public_html/temp/index.php(28): Utils\LoadJson-&gt;getData()
#1 {main}
  thrown in <b>/var/www/pressabl/public_html/temp/php/Utils/LoadJson.php</b> on line <b>47</b><br />
';

//echo 'Do the interfaces here make sense? index.php is the executing file:';
echo 'Why do I get this error, with the following files?';

echo "\n\n";
echo $error;
echo "\n\n";


$files = [
    'php/Utils/LoadJson.php',
    'php/Interfaces/LoadJsonInterface.php',
    'index.php',
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