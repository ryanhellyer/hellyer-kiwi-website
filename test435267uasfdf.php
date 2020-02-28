<?php
set_time_limit( 60 * 5 );

$iterations_to_do = '';
if ( isset( $_POST['iterations'] ) ) {
	$iterations_to_do = (int) $_POST['iterations'];
}

$form = '
<form action="" method="POST">
	<label>Iterations (maxes out around 32 ish)</label>
	<input type="number" name="iterations" value="' . $iterations_to_do . '" />

	<br /><br />
	<input type="submit" name="submit" value="submit" />
</form>';

if ( ! isset( $_POST['iterations'] ) ) {
	echo $form;
	die;
}

$args = array(
	1  => 'Plugin off',
	2  => 'Slowest 1 ',
	3  => 'Test 2    ',
	4  => 'Test 3    ',
	5  => 'Test 4    ',
	6  => 'Test 5    ',
	7  => 'Fastest 6 ',
	8  => 'Plugin on ',
);

$url = 'ab -A zsuraski:M1qbewV6D9 -n 100 -c 20 https://zsuraski.site.strattic.io/?test=';
echo '<pre>' . $url . '</pre><br />';

$iterations = 0;
$iterations_to_do = $iterations_to_do * count( $args );
while ( $iterations < $iterations_to_do ) {

	$count++;

	$requests = shell_exec( $url . $count . ' | grep Request' );
	$requests = str_replace( 'Requests per second:    ', '', $requests );
	$requests = str_replace( ' [#/sec] (mean)', '', $requests );

	$results[ $count ][] = $requests;

	if ( count( $args ) === $count ) {
		$count = 0;
	}

	$iterations++;
}

echo '<pre>';
foreach ( $args as $count => $label ) {
	$number  = count( $results[ $count ] );
	$average = array_sum( $results[ $count ] ) / $number;
	$max     = trim( max( $results[ $count ] ) );
	$min     = trim( min( $results[ $count ] ) );

	echo $label . ' average: ' . round( $average, 2 ) . ' (from: ' . $number . ' tests; max: ' . $max . '; min: ' . $min . ') ' . '<br />';
}
echo '</pre>';

echo '<br /><br />';
echo $form;
