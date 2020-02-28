<?php

$iterations = 0;
while ( $iterations < 6 ) {

	$count++;

	$requests = shell_exec( 'ab -A zsuraski:M1qbewV6D9 -n 100 -c 20 https://zsuraski.site.strattic.io/?test=' . $count . ' | grep Request' );
	$requests = str_replace( 'Requests per second:    ', '', $requests );
	$requests = str_replace( ' [#/sec] (mean)', '', $requests );

	$results[ $count ][] = $requests;

	if ( 3 === $count ) {
		$count = 0;
	}

	$iterations++;
}

foreach ( array(
	1 => 'Test 1',
	2 => 'Plugin on',
	3 => 'Plugin off'
) as $count => $label ) {
	$count   = count( $results[ $count ] );
	$average = array_sum( $results[ $count ] ) / $count;
	$max     = max( $results[ $count ] );
	$min     = min( $results[ $count ] );

	echo $label . ' average: ' . $average . ' (from: ' . $count . ' tests; max: ' . $max . '; min: ' . $min . ') <br />';
}
