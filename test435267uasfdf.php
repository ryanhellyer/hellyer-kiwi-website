<?php

$number = 0;
while ( $number < 60 ) {

	if ( $number % 2 == 0 ) {
		$on = 'on';
	} else {
		$on = 'of';
	}

	$requests = shell_exec( 'ab -A zsuraski:M1qbewV6D9 -n 100 -c 20 https://zsuraski.site.strattic.io/?test=' . $on . ' | grep Request');
	$requests = str_replace( 'Requests per second:    ', '', $requests );
	$requests = str_replace( ' [#/sec] (mean)', '', $requests );

	$results[ $on ][] = $requests;

	$number++;
}


foreach ( array(
	'01' => 'Test 1',
	'on' => 'Plugin on',
	'of' => 'Plugin off'
) as $on => $label ) {
	$count   = count( $results[ $on ] );
	$average = array_sum( $results[ $on ] ) / $count;
	$max     = max( $results[ $on ] );
	$min     = min( $results[ $on ] );

	echo $label . ' average: ' . $average . ' (from: ' . $count . ' tests; max: ' . $max . '; min: ' . $min . ') <br />';
}

	// > monitor.txt