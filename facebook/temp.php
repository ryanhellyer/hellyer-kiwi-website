<?php
$bla = file_get_contents( 'index.php' );
$bla = str_replace( "\n\n", "\n", $bla );
echo $bla;
