<?php

/**
 * Register invoice post-type.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class WP_Invoice_Invoice_Post_Type extends WP_Invoice_Core {

	/*
	 * Class constructor.
	 */
	public function __construct() {

		// Get the invoice ID
		if ( isset( $_GET['post'] ) ) {
			$this->invoice_id = $_GET['post'];
		} else {
			$this->invoice_id = 0;
		}

		add_action( 'init',           array( $this, 'register_post_type' ) );

		// buttons metabox
		add_action( 'add_meta_boxes', array( $this, 'add_buttons_metabox' ) );
		add_action( 'save_post',      array( $this, 'meta_buttons_boxes_save' ), 11, 2 );

		// Client metabox
		add_action( 'add_meta_boxes', array( $this, 'add_client_metabox' ) );
		add_action( 'save_post',      array( $this, 'meta_client_boxes_save' ), 10, 2 );

		// Date metabox
		add_action( 'add_meta_boxes', array( $this, 'add_invoice_date_metabox' ) );
		add_action( 'save_post',      array( $this, 'meta_invoice_date_boxes_save' ), 10, 2 );

		// Invoice meta metabox
		add_action( 'add_meta_boxes', array( $this, 'add_invoice_from_metabox' ) );
		add_action( 'save_post',      array( $this, 'meta_invoice_from_boxes_save' ), 10, 2 );

		// Entries meta box
		add_action( 'add_meta_boxes', array( $this, 'add_entries_metaboxes' ) );
		add_action( 'save_post',      array( $this, 'meta_entries_boxes_save' ), 10, 2 );
		add_action( 'admin_footer',   array( $this, 'scripts' ) );

	}

	/**
	 ** Register post-type.
	 */
	public function register_post_type() {

		$args = array(
			'public'             => true,
			'publicly_queryable' => true,
			'label'              => __( 'Invoice', 'wp-invoice' ),
			'supports'           => array(
				'title',
			)
		);
		register_post_type( 'invoice', $args );

	}

	/**
	 * Add client metabox.
	 */
	public function add_buttons_metabox() {
		add_meta_box(
			'buttons', // ID
			__( 'Actions', 'wp-invoice' ), // Title
			array(
				$this,
				'buttons_meta_box', // Callback to method to display HTML
			),
			'invoice', // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Add meta box with buttons.
	 */
	public function buttons_meta_box() {
		echo '
		<p>
			<input name="import-entries" type="submit" class="button button-large" value="' . esc_html__( 'Import entries', 'wp-invoice' ) . '" />
		</p>
		<p>
			<input name="removal-all-entries" type="submit" class="button button-large" value="' . esc_html__( 'Removal all entries', 'wp-invoice' ) . '" />
		</p>
		<p>
			<input name="combine-identical-entries" type="submit" class="button button-large" value="' . esc_html__( 'Combine identical entries', 'wp-invoice' ) . '" />
		</p>
		<input type="hidden" id="buttons-nonce" name="buttons-nonce" value="' . esc_attr( wp_create_nonce( __FILE__ ) ) . '">
';
	}

	/**
	 * Save buttons meta box data.
	 *
	 * @param  int     $invoice_id  The post ID
	 * @param  object  $post        The post object
	 */
	public function meta_buttons_boxes_save( $invoice_id, $post ) {

		// Only save if correct post data sent
		if ( isset( $_POST['buttons-nonce'] ) ) {

			// Do nonce security check
			if ( ! wp_verify_nonce( $_POST['buttons-nonce'], __FILE__ ) ) {
				return $post_id;
			}

			// Import entries
			if ( isset( $_POST['import-entries'] ) ) {

				$start_date = esc_html( $_POST['_start_date'] );
				$end_date   = esc_html( $_POST['_end_date'] );

				// Only process the required client (need to run query because we don't know what it's ID is at this point)
				$clients_query = new WP_Query(
					array(
						'no_found_rows'          => true,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
						'posts_per_page'         => 10,
						'post_type'              => 'client',
						'title'                  => esc_html( $_POST['_client'] ),
					)
				);

				$clients = array();
				if ( $clients_query->have_posts() ) {
					while ( $clients_query->have_posts() ) {
						$clients_query->the_post();

						// Get all entries for this client
						$entries_query = new WP_Query(
							array(
								'posts_per_page'         => 1000,
								'no_found_rows'          => true,
								'update_post_meta_cache' => false,
								'update_post_term_cache' => false,
								'fields'                 => 'ids',
								'post_type'              => 'entry',
								'post_parent'            => get_the_ID(),
								'date_query' => array(
									array(
										'after'     => $start_date,
										'before'    => $end_date,
										'inclusive' => true,
									),
								),
							)
						);
						$data = array();
						$count = 0;
						if ( $entries_query->have_posts() ) {
							while ( $entries_query->have_posts() ) {
								$entries_query->the_post();

								$start_date_timestamp = get_post_meta( get_the_ID(), '_start_date', true );
								$start_date = date( 'Y-m-d', $start_date_timestamp );
								$start_time = date( 'H:i:s' );
								$end_date_date = get_the_date( 'Y-m-d', get_the_ID() );
								$end_date_timestamp = get_the_date( 'U', get_the_ID() );
								$end_time = get_the_date( 'H:i:s', get_the_ID() );
								$hours = round( ( $end_date_timestamp - $start_date_timestamp ) / 60 / 15) / 4;

								$data[$count]['entry_ids'][]  = get_the_ID();
								$data[$count]['title']      = get_the_title( get_the_ID() );

$data[$count]['project'] = '';
								$data[$count]['start-date'] = $start_date;
								$data[$count]['start-time'] = $start_time;

								$data[$count]['end-date']   = $end_date_date;
								$data[$count]['end-time']   = $end_time;

								$data[$count]['hours']      = $hours;

								$count++;
							}
						}

						update_post_meta( $invoice_id, '_wp_invoice_entries', $data );

					}
				}

			}

			// Remove all entries
			if ( isset( $_POST['removal-all-entries'] ) ) {
				delete_post_meta( $invoice_id, '_wp_invoice_entries' );
			}

			// Combine identical entries
			if ( isset( $_POST['combine-identical-entries'] ) ) {
				$entries = get_post_meta( $invoice_id, '_wp_invoice_entries', true );
				$entries = $this->combine_entries( $entries );
				update_post_meta( $invoice_id, '_wp_invoice_entries', $entries );
			}

		}

	}


	/**
	 * Add client metabox.
	 */
	public function add_client_metabox() {
		add_meta_box(
			'client', // ID
			__( 'Client', 'wp-invoice' ), // Title
			array(
				$this,
				'client_meta_box', // Callback to method to display HTML
			),
			'invoice', // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the client meta box.
	 */
	public function client_meta_box() {

		$current_client = get_post_meta( $this->invoice_id, '_client', true );

		$clients_query = new WP_Query(
			array(
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'posts_per_page'         => 100,
				'post_type'              => 'client',
			)
		);
		$clients = array();
		if ( $clients_query->have_posts() ) {
			while ( $clients_query->have_posts() ) {
				$clients_query->the_post();
				$clients[get_the_ID()] = get_the_title( get_the_ID() );
			}
		}

		?>

		<p>
			<label for="_client"><?php _e( 'Client', 'wp-invoice' ); ?></label>
			<br />
			<select name="_client" id="_client"><?php

			foreach ( $clients as $key => $client ) {
				echo '<option ' . selected( $client, $current_client, false )  . ' value="' . esc_attr( $client ) . '">' . esc_html( $client ) . '</option>';
			}

			?></select>

		</p>

		<input type="hidden" id="client-nonce" name="client-nonce" value="<?php echo esc_attr( wp_create_nonce( __FILE__ ) ); ?>">

		<?php
	}

	/**
	 * Save client meta box data.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function meta_client_boxes_save( $post_id, $post ) {

		// Only save if correct post data sent
		if ( isset( $_POST['_client'] ) ) {

			// Do nonce security check
			if ( ! wp_verify_nonce( $_POST['client-nonce'], __FILE__ ) ) {
				return $post_id;
			}

			// Sanitize and store the data
			$client = wp_kses_post( $_POST['_client'] );

			update_post_meta( $post_id, '_client', $client );

		}

	}

	/**
	 * Add invoice date metabox.
	 */
	public function add_invoice_date_metabox() {
		add_meta_box(
			'invoice-date', // ID
			__( 'Invoice date range', 'wp-invoice' ), // Title
			array(
				$this,
				'invoice_date_meta_box', // Callback to method to display HTML
			),
			'invoice', // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the invoice date meta box.
	 */
	public function invoice_date_meta_box() {

		$start_date_timestamp = get_post_meta( $this->invoice_id, '_start_date', true );
		if ( '' !== $start_date_timestamp ) {
			$start_date = date( self::DATE_FORMAT, (int) $start_date_timestamp );
		} else {
			$start_date = date( self::DATE_FORMAT );
		}

		$end_date = get_the_date( self::DATE_FORMAT, $this->invoice_id );

		?>

		<style>#minor-publishing{ display: none; }</style>

		<p>
			<label for="_start_date"><?php _e( 'Start date', 'wp-invoice' ); ?></label>
			<br />
			<input type="date" name="_start_date" id="_start_date" value="<?php echo esc_attr( $start_date ); ?>" />
		</p>

		<p>
			<label for="_end_date"><?php _e( 'End date', 'wp-invoice' ); ?></label>
			<br />
			<input type="date" name="_end_date" id="_end_date" value="<?php echo esc_attr( $end_date ); ?>" />
		</p>

		<input type="hidden" id="date-nonce" name="date-nonce" value="<?php echo esc_attr( wp_create_nonce( __FILE__ ) ); ?>">

		<?php
	}

	/**
	 * Save invoice date meta box data.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function meta_invoice_date_boxes_save( $invoice_id, $post ) {

		// Only save if correct post data sent
		if ( isset( $_POST['_start_date'] ) && isset( $_POST['_end_date'] ) ) {

			// Do nonce security check
			if ( ! wp_verify_nonce( $_POST['date-nonce'], __FILE__ ) ) {
				return $invoice_id;
			}

			// Sanitize and store the data
			$start_date = absint( strtotime( $_POST['_start_date'] . ' 00:00:00' ) );
			$end_date = date( self::DATE_FORMAT, absint( strtotime( $_POST['_end_date'] ) ) ) . ' 23:59:59';

			update_post_meta( $invoice_id, '_start_date', $start_date );

			remove_action( 'save_post', array( $this,'meta_invoice_date_boxes_save' ) );
			wp_update_post(
				array(
					'ID'            => $invoice_id,
					'post_date'     => $end_date,
					'post_date_gmt' => get_gmt_from_date( $end_date ),
				)
			);
			add_action( 'save_post', array( $this, 'meta_invoice_date_boxes_save' ), 10, 2 );

		}

	}

	/**
	 * Add invoice from metabox.
	 */
	public function add_invoice_from_metabox() {
		add_meta_box(
			'invoice-from', // ID
			__( 'Invoice meta', 'wp-invoice' ), // Title
			array(
				$this,
				'invoice_from_meta_box', // Callback to method to display HTML
			),
			'invoice', // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the invoice from meta box.
	 */
	public function invoice_from_meta_box() {

		$meta_keys = array(
			'from',
			'number',
			'details',
			'currency',
			'due_date',
			'hourly_rate',
			'note',
			'paid',
			'bank_details',
		);

		$meta_data = array();
		foreach ( $meta_keys as $meta_key ) {
			$meta_data[ $meta_key ] = get_post_meta( $this->invoice_id, '_invoice_' . $meta_key, true );
			$client = get_post_meta( $this->invoice_id, '_client', true );

			if (
				'' === $meta_data[ $meta_key ]
				&&
				'' !== $client
			) {

				$clients_query = new WP_Query(
					array(
						'posts_per_page'         => 1,
						'no_found_rows'          => true,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
						'fields'                 => 'ids',
						'order'                  => 'DESC',
						'orderby'                => 'date',
						'post_type'              => 'invoice',
						'meta_key'               => '_client',
						'meta_value'             => $client,
						'meta_compare'           => '==',
					)
				);
				if ( $clients_query->have_posts() ) {
					while ( $clients_query->have_posts() ) {
						$clients_query->the_post();

						if ( $this->invoice_id !== get_the_ID() ) {
							$meta_data[ $meta_key ] = get_post_meta( get_the_ID(), '_invoice_' . $meta_key, true );
						}

					}

				}

			}


		}

		?>

		<p>
			<label for="_invoice_from"><?php _e( 'Invoice from', 'wp-invoice' ); ?></label>
			<br />
			<textarea name="_invoice_from" id="_invoice_from"><?php echo esc_textarea( $meta_data['from'] ); ?></textarea>
		</p>

		<p>
			<label for="_invoice_number"><?php _e( 'Invoice number', 'wp-invoice' ); ?></label>
			<br />
			<input type="text" name="_invoice_number" id="_invoice_number" value="<?php echo esc_attr( $meta_data['number'] ); ?>" />
		</p>

		<p>
			<label for="_invoice_details"><?php _e( 'Details', 'wp-invoice' ); ?></label>
			<br />
			<input type="text" name="_invoice_details" id="_invoice_details" value="<?php echo esc_attr( $meta_data['details'] ); ?>" />
		</p>

		<p>
			<label for="_invoice_currency"><?php _e( 'Currency', 'wp-invoice' ); ?></label>
			<br />
			<select name="_invoice_currency" id="_invoice_currency">

			<?php
			$currencies = array(
				'EUR',
				'USD',
				'NZD',
				'NOK',
			);
			$current_currency = $meta_data['currency'];
			foreach ( $currencies as $key => $currency ) {
				echo '<option ' . selected( $currency, $current_currency, false )  . ' value="' . esc_attr( $currency ) . '">' . esc_html( $currency ) . '</option>';
			}
			?>
			</select>
		</p>

		<p>
			<label for="_invoice_due_date"><?php _e( 'Due date', 'wp-invoice' ); ?></label>
			<br />
			<input type="date" name="_invoice_due_date" id="_invoice_due_date" value="<?php echo esc_attr( $meta_data['due_date'] ); ?>" />
		</p>
 
		<p>
			<label for="_invoice_hourly_rate"><?php _e( 'Hourly rate ', 'wp-invoice' ); ?></label>
			<br />
			<input type="number" name="_invoice_hourly_rate" id="_invoice_hourly_rate" value="<?php echo esc_attr( $meta_data['hourly_rate'] ); ?>" />
		</p>

		<p>
			<label for="_invoice_note"><?php _e( 'Note', 'wp-invoice' ); ?></label>
			<br />
			<input type="text" name="_invoice_note" id="_invoice_note" value="<?php echo esc_attr( $meta_data['note'] ); ?>" />
		</p>

		<p>
			<label for="_invoice_paid"><?php _e( 'Paid? ', 'wp-invoice' ); ?></label>
			<br />
			<input type="checkbox" <?php checked( $meta_data['paid'], true ); ?> name="_invoice_paid" id="_invoice_paid" value="1" />
		</p>

		<p>
			<label for="_invoice_bank_details"><?php _e( 'Bank details ', 'wp-invoice' ); ?></label>
			<br />
			<textarea name="_invoice_bank_details" id="_invoice_bank_details"><?php echo esc_textarea( $meta_data['bank_details'] ); ?></textarea>
		</p>

		<input type="hidden" id="from-nonce" name="from-nonce" value="<?php echo esc_attr( wp_create_nonce( __FILE__ ) ); ?>">

		<?php
	}

	/**
	 * Save invoice from meta box data.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function meta_invoice_from_boxes_save( $post_id, $post ) {

		// Bail out if not on correct post-type
		if ( 'invoice' !== get_post_type() ) {
			return;
		}

		// Do nonce security check
		if ( isset( $_POST['_invoice_from'] ) ) {

			if ( ! wp_verify_nonce( $_POST['from-nonce'], __FILE__ ) ) {
				return $post_id;
			}
		}

		if ( isset( $_POST['_invoice_from'] ) ) {
			$invoice_from = wp_kses_post( $_POST['_invoice_from'] );
			update_post_meta( $post_id, '_invoice_from', $invoice_from );
		}

		if ( isset( $_POST['_invoice_number'] ) ) {
			$invoice_number = wp_kses_post( $_POST['_invoice_number'] );
			update_post_meta( $post_id, '_invoice_number', $invoice_number );

			// Set post slug as invoice number (note, needs to be changed each time, as for some bizarre reason WordPress keeps resetting it to whatever it thinks is best)
			$author_id = get_post_field ( 'post_author'