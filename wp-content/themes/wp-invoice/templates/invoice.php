<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php wp_head(); ?>
</head>
<body>

<header>
	<div class="light-colour"><div class="block"></div></div>
	<div class="medium-colour"><div class="block"></div></div>
	<div class="dark-colour"><div class="block"></div><h1>Invoice <span>Ryan Hellyer</span></h1></div>
</header>

<main>

	<p class="box invoice-to">
		<em>Invoice to:</em>
		<strong class="invoice-to-name"><?php echo esc_html( $client_name ); ?></strong>
		<span class="invoice-to-details"><?php echo wp_kses_post( str_replace( "\n", '<br />', $data[ '_invoice_to_details' ] ) ); ?></span>
		<a class="invoice-to-website" href="#"><?php echo esc_html( $website ); ?></a>
		</span>
	</p>

	<p class="box invoice-from">
		Tax identity number: 16/339/01057<br />
		Ryan Hellyer<br />
		Friedrichstraße 123<br />
		10117 Berlin<br />
		Deutschland<br />
		<br />
		<em>Total Due:</em>
		<strong class="total-amount"><?php echo esc_html( $data['_currency'] . $total_amount ); ?></strong>
	</p>

	<table>
		<thead>
			<tr>
				<th>Description</th>
				<th>Due date</th>
				<th>Hours</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2"></td>
				<td>Total amount</td>
				<td class="total-amount"><?php echo esc_html( $data['_currency'] . $total_amount ); ?></td>
			</tr>
		</foot>
		<tbody><?php

// Outputting the tasks
foreach ( $data['_tasks'] as $key => $task ) {

	// Ensuring variables exist, to avoid debug errors
	foreach ( $this->possible_keys as $possible_key => $field ) {
		if ( ! isset( $task[ $possible_key ] ) ) {
			$task[ $possible_key ] = '';
		}

	}

	echo '
			<tr>
				<td>
					<strong>' . esc_html( $task[ 'title' ] ) . '</strong>
					' . esc_html( $task[ 'description' ] ) . '
				</td>
				<td>' . esc_html( $data[ '_due_date' ] ) . '</td>
				<td>' . esc_html( $task[ 'hours' ] ) . '</td>
				<td>' . esc_html( $data['_currency'] . $task[ 'amount' ] ) . '</td>
			</tr>';
}


?>

		</tbody>
	</table>

	<p class="box bank-details">
		<strong>Bank details for direct deposit</strong>
		Berliner Sparkasse:<br />
		Ryan Hellyer<br />
		Account number: 1063737628<br />
		IBAN: DE 93 1005 0000 1063737628<br />
		BIC: BELADEBE<br />
		Address: Rankestraße 33­34, 10789 Berlin, Deutschland<br />
	</p>

</main>

<footer>
	<p>Thank your for your business!</p>
	<p>ryanhellyer@gmail.com | geek.hellyer.kiwi</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>