<?php

$args = array(
	1  => 'Test 1    ',
	2  => 'Test 2    ',
	3  => 'Test 3    ',
	4  => 'Test 4    ',
	5  => 'Test 5    ',
	6  => 'Test 6    ',
	7  => 'Test 7    ',
	8  => 'Test 8    ',
	9  => 'Test 9    ',
	10 => 'Test 10    ',
	11 => 'Plugin on ',
	12 => 'Plugin off',
);

$iterations = 0;
while ( $iterations < 30 ) {

	$count++;

	$requests = shell_exec( 'ab -A zsuraski:M1qbewV6D9 -n 10 -c 5 https://zsuraski.site.strattic.io/?test=' . $count . ' | grep Request' );
	$requests = str_replace( 'Requests per second:    ', '', $requests );
	$requests = str_replace( ' [#/sec] (mean)', '', $requests );

	$results[ $count ][] = $requests;

	if ( count( $args ) === $count ) {
		$count = 0;
	}

	$iterations++;
}

foreach ( $args as $count => $label ) {
	$number  = count( $results[ $count ] );
	$average = array_sum( $results[ $count ] ) / $number;
	$max     = trim( max( $results[ $count ] ) );
	$min     = trim( min( $results[ $count ] ) );

	echo $label . ' average: ' . round( $average, 2 ) . ' (from: ' . $count . ' tests; max: ' . $max . '; min: ' . $min . ') ' . "\n";
}
