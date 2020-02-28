<?php

$iterations = 0;
while ( $iterations < 3 ) {

	$count++;

	$requests = shell_exec( 'ab -A zsuraski:M1qbewV6D9 -n 100 -c 20 https://zsuraski.site.strattic.io/?test=' . $count . ' | grep Request' );
	$requests = str_replace( 'Requests per second:    ', '', $requests );
	$requests = str_replace( ' [#/sec] (mean)', '', $requests );

	if ( 3 === $count ) {
		$count = 0;
	}

	$results[ $on ][] = $requests;

	$iterations++;
}


foreach ( array(
	'1' => 'Test 1',
	'2' => 'Plugin on',
	'3' => 'Plugin off'
) as $on => $label ) {
	$count   = count( $results[ $on ] );
	$average = array_sum( $results[ $on ] ) / $count;
	$max     = max( $results[ $on ] );
	$min     = min( $results[ $on ] );

	echo $label . ' average: ' . $average . ' (from: ' . $count . ' tests; max: ' . $max . '; min: ' . $min . ') <br />';
}

	// > monitor.txt