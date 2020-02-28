<?php
set_time_limit( 60 * 5 );
$time_start = microtime( true ); 

$iterations_to_do = '';
if ( isset( $_POST['iterations'] ) ) {
	$iterations_to_do = (int) $_POST['iterations'];
}
$password = '';
if ( isset( $_POST['password'] ) ) {
	$password =  filter_var( $_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS );
}

$form = '
<form action="" method="POST">
	<input style="width:100px" type="number" name="iterations" value="' . $iterations_to_do . '" />
	<label>Iterations (maxes out around 16 ish which takes ~2 mins)</label>

	<br />

	<input style="width:100px" type="text" name="password" value="' . $password . '" />
	<label>Password</label>

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

$url = 'ab -A zsuraski:' . $password . ' -n 100 -c 20 https://zsuraski.site.strattic.io/?test=';
echo '<pre>' . $url . '</pre><br />';

$iterations = 0; // Need to start less than zero to warm the site up.
while ( $iterations < $iterations_to_do ) {

	foreach ( $args as $count => $label ) {

		$requests = shell_exec( $url . $count . ' | grep Request' );
		$requests = str_replace( 'Requests per second:    ', '', $requests );
		$requests = str_replace( ' [#/sec] (mean)', '', $requests );

		if ( $iterations > 0 ) {
			$results[ $count ][] = $requests;
		}
		$raw_results[ $count ][] = $requests;
	}

	$iterations++;
}

echo '<label>Raw results data</label>';
echo '<textarea style="width:100%;">';print_r( $raw_results );echo '</textarea>';

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



$time_end = microtime( true );
$execution_time = ( $time_end - $time_start );
echo '<br /><br />Total Execution Time: ' . $execution_time . ' s';
