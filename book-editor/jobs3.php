<?php

header( 'Content-Type: text/xml' );
header( 'Access-Control-Allow-Origin: *' );

header_remove( 'Referrer Policy: strict-origin-when-cross-origin' );
header_remove( 'strict-origin-when-cross-origin' );
header_remove( 'Referrer Policy' );


echo file_get_contents( 'https://edesk.peoplehr.net/Pages/JobBoard/CurrentOpenings.aspx?o=fde84bea-edba-41dd-926f-8ec83870a0d8' );
die;
