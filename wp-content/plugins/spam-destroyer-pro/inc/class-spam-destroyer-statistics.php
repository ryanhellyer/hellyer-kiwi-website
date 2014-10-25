<?php


/**
 * Tracking of users
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Spam_Destroyer_Statistics extends Spam_Destroyer {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'spam_track_stats',   array( $this, 'increment_stats' ) ); // Mark an extra spam in the stats
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) ); // Add dashboard widget
		add_action( 'spam_comment',       array( $this, 'record_false_positive' ) );
		add_action( 'ham_comment',        array( $this, 'record_legit_comment' ) );
		add_action( 'spam_comment',       array( $this, 'change_protection_level_automatically' ) );
		add_action( 'init',               array( $this, 'set_protection_level' ) );
		add_action( 'init',               array( $this, 'output_stats_for_testing' ) );
	}
	
	/**
	 * For testing purposes only
	 */
	public function output_stats_for_testing() {
		if ( !isset( $_GET['spam_destroyer_testing'] ) )
			return;

		print_r( get_option( 'spam-killer-stats' ) );
		die;
	}
	
	/*
	 * Set protection level
	 */
	public function set_protection_level() {
		$this->protection = get_option( 'spam_destroyer_level' );
	}

	/*
	 * Calculate the ratio of spam to ham and adjust protection level accordingly
	 */
	public function change_protection_level_automatically() {
		$false = $this->get_stats( 'false', $time = 'previous' );
		$spam = $this->get_stats( 'spam', $time = 'previous' );
		$rate = ( 1 - ( $false/ $spam ) ) * 100;
		if ( $rate > 99.9 ) {
			update_option( 'spam_destroyer_level', 'low' );
		} else {
			update_option( 'spam_destroyer_level', 'high' );
		}
	}

	/*
	 * Record reported false positives
	 * We need to know when users have marked something as spam for stats analysis
	 */
	public function record_false_positive() {
		$this->increment_stats( 'false' );
	}
	
	/*
	 * Record ham result
	 */
	public function record_legit_comment() {
		$this->increment_stats( 'ham' );
	}

	/*
	 * Add the dashboard widget
	 */
	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'dashboard_spam_destroyer',
			__( 'Spam Destroyer', 'spam-destroyer' ),
			array( $this, 'dashboard_widget' )
		);
	}

	/*
	 * The dashboard widget content
	 */
	public function dashboard_widget() {
		$spam  = $this->get_stats( 'spam', $time = 'previous' );
		$start = $this->get_stats( 'start', $time = 'previous' );
		$end   = $this->get_stats( 'end', $time = 'previous' );
		$time  = $end - $start;
		$hours = $time / ( 60 * 60 * 24 );
		
		// Can't do a rates if we don't have data yet (can't divide by zero)
		if ( $spam != 0 && $hours != 0 && $hours != 0 ) {
			echo '<p>';
			echo __( 'You are currently receiving spam at a rate of approximately', 'spam-destroyer' ) . ' ';
			$rate = (int) ( $spam / $hours );
			echo $rate . ' per day';
			echo '</p>';
			
			echo '<p>';
			echo __( 'Spam Destroyer blocked ', 'spam-destroyer' ) . ' ';
			$ham = $this->get_stats( 'ham', $time = 'previous' );
			$false = $this->get_stats( 'false', $time = 'previous' );
			$spam = $this->get_stats( 'spam', $time = 'previous' );
//			echo '<br><br>' . $ham . '<br>' . $false . '<br>' . $spam . '<br><br>';
			$rate = 1 - ( $false / ( $spam + $false + $ham ) ); // Calculate previous rate of spam blockage
			$rate = $rate * 100; // Convert rate to percentage
			$rate = round( $rate, 4 ); // Set figures to something reasonable
			echo $rate . '% ' . __( 'of spam between', 'spam-destroyer' ) . ' ';
			$start = $this->get_stats( 'start', 'previous' );
			$start = date( 'h:m d M Y', $start );
			$end = $this->get_stats( 'end', 'previous' );
			$end = date( 'h:m d M Y', $end );
			echo $start . __( ' and ', 'spam-destroyer' ) . $end;
			echo '</p>';
			
			echo '<p>';
			echo __( 'Spam Destroyer has blocked ', 'spam-destroyer' ) . ' ';
			$ham = $this->get_stats( 'ham', $time = 'previous' );
			$false = $this->get_stats( 'false', 'total' );
			$spam = $this->get_stats( 'spam', 'total' );
//			echo '<br><br>' . $ham . '<br>' . $false . '<br>' . $spam . '<br><br>';
			$rate = 1 - ( $false / ( $spam + $false + $ham ) ); // Calculate previous rate of spam blockage
			$rate = $rate * 100; // Convert rate to percentage
			$rate = round( $rate, 4 ); // Set figures to something reasonable
			echo $rate . '% ' . __( 'of spam since ', 'spam-destroyer' ) . ' ';
			$start = $this->get_stats( 'start', 'total' );
			$start = date( 'h:m d M Y', $start );
			echo $start;
			echo '</p>';
		} else {
			echo '<p>';
			echo __( 'Statistics will appear here once Spam Destroyer has blocked some spam.', 'spam-destroyer' ) . ' ';
			echo '</p>';
		}
		echo '<div class="clear"></div>';
	}

	/*
	 * Output stats. info.
	 */
	public function get_stats( $type = 'spam', $time = 'current' ) {
		$stats = get_option( 'spam-killer-stats' );
		$info = $stats[$time];
		if ( 'combined' == $type ) {
			$required_stats = $info['spam'] + $info['ham'];
		} elseif ( isset( $info[$type] ) ) {
			$required_stats = $info[$type];
		} else {
			$required_stats = 0;
		}
		return $required_stats;
	}

	/*
	 * Get list of plugins for sending home
	 * Useful for tracking which other spam plugins users are using
	 */
	public function get_plugin_list() {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$plugins = get_plugins();
		$plugin_list = '';
		foreach( $plugins as $plugin => $value ) {
			if ( is_plugin_active( $plugin ) ) {
				$plugin = explode( '/', $plugin );
				$plugin = $plugin[0];
				$plugin_list .= $plugin . '|';
			}
		}
		return $plugin_list;
	}

	/*
	 * Increment statistics
	 *
	 * @param string $type Spam or ham, that is the question.
	 */
	public function increment_stats( $type = 'spam' ) {

		// Bail out now if they've chosen not to log stuff
		if ( false == SPAM_DESTROYER_LOGGING ) {
			return;
		}

		// Need to specify $type
		if ( empty( $type ) )
			$type = 'spam';

		$stats = get_option( 'spam-killer-stats' );
		
		// Add defaults if no stats stored yet
		if ( ! is_array( $stats ) ) {
			$stats = array(
				'current' => array(
					'spam'  => 0,
					'ham'   => 0,
					'start' => time(),
					'false' => 0,
				),
				'previous' => array(
					'spam'  => 0,
					'ham'   => 0,
					'false' => 0,
				),
				'total' => array(
					'spam'  => 0,
					'ham'   => 0,
					'start' => time(),
					'false' => 0,
				)
			);
			add_option( 'spam-killer-stats', $stats, '', 'no' );
		}

		// Iterate existing stats
		$current          = $stats['current'];
		$total            = $stats['total'];
		$current[$type]   = $current[$type] + 1;
		$total[$type]     = $total[$type] + 1;
		$stats['total']   = $total;
		$stats['current'] = $current;

		// Store them in convenient time blocks
		$current_total = $current['spam'] + $current['ham'];
		if ( $current_total > SPAM_DESTROYER_STATS_BLOCK_SIZE ) {

			$previous = $stats['previous'];
			$current['end'] = time();
			$stats['previous'] = $current;
			$stats['current'] = array(
				'start' => time(),
				'spam'  => 0,
				'ham'   => 0
			);

			$domain = home_url();
			$previous_spam  = $previous['spam'];
			$previous_ham   = $previous['ham'];
			$previous_false = $previous['false'];
			$previous_start = $previous['start'];
			$previous_end   = $previous['end'];

			$total_spam     = $total['spam'];
			$total_ham      = $total['ham'];
			$total_false    = $total['false'];
			$total_start    = $total['start'];

			// Send request to API (response is ignored since it doesn't matter that much if it doesn't make it through)
//			$response = wp_remote_get( $url );

			$response = wp_remote_post(
				SPAM_DESTROYER_API,
				array(
					'method'      => 'POST',
//					'timeout'     => 45,
//					'redirection' => 5,
//					'httpversion' => '1.0',
//					'blocking'    => true,
//					'headers'     => array(),
//					'cookies'     => array(),
					'body'        => array(
						'domain'         => $domain,
						'previous_spam'  => $previous_spam,
						'previous_ham'   => $previous_ham,
						'previous_false' => $previous_false,
						'previous_start' => $previous_start,
						'previous_end'   => $previous_end,
						'total_spam'     => $total_spam,
						'total_ham'      => $total_ham,
						'total_false'    => $total_false,
						'total_start'    => $total_start,
						'plugins'        => $this->get_plugin_list(),
						'ver'            => SPAM_DESTROYER_VERSION,
					),
				)
			);
		}

		// Store the data
		update_option( 'spam-killer-stats', $stats );
//echo $url;die;

	}

}

