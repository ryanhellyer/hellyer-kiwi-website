<?php

/**
 * WP Cron tasks.
 */
class SRC_Auto_News {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_filter( 'the_content', array( $this, 'questionaire' ), 9 );
	}

	static private function get_interview_query( $event_id ) {
		$args = array(
			'posts_per_page'         => 1,
			'post_type'              => 'post',
			'post_status'            => array( 'publish', 'draft' ),
			'post_type'              => 'post',
			'meta_key'               => 'interview',
			'meta_value'             => $event_id,
			'no_found_rows'          => true,  // useful when pagination is not needed.
			//'update_post_meta_cache' => false, // useful when post meta will not be utilized.
			'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
			'fields'                 => 'ids'
		);
		$query = new WP_Query( $args );

		return $query;
	}

	static public function auto_news() {

		// Get latest event IDs.
		$query = new WP_Query( array(
			'post_type'      => 'event',
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'no_found_rows'  => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields'         => 'ids'
		) );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$event_date = get_post_meta( get_the_ID(), 'date', true );

				unset( $time );
				foreach ( array( 3, 2, 1 ) as $race ) {
					$race_time = get_post_meta( get_the_ID(), 'race_' . $race . '_time', true );

					if ( '' !== $race_time && ! isset( $time ) ) {
						$exploded_race_time = explode( ':', $race_time );
						$hours_in_seconds   = $exploded_race_time[0] * 60 * 60;
						$minutes_in_seconds = $exploded_race_time[1] * 60;
						$time               = $hours_in_seconds + $minutes_in_seconds;
						$event_time         = $event_date + $time;

						if ( $event_time > time() ) {
							continue; // Do nothing because this event is in the future.
						} else if ( 
							( time() - $event_time )
							<
							WEEK_IN_SECONDS
						) {
							$event_ids[] = get_the_ID();
						}

					}

				}
			}
		}

		// Loop through every event ID to see if there's a match.
		foreach ( $event_ids as $event_id ) {
			$query = self::get_interview_query( $event_id );
			if ( ! $query->have_posts() ) {

				$args = array(
					'post_title'    => 'My post',
					'post_content'  => 'This is my post.',
					'post_status'   => 'draft',
					'post_author'   => 1,
				);
				$post_id = wp_insert_post( $args );
				add_post_meta( $post_id, 'interview', $event_id );
			} else {
				/*
				while ( $query->have_posts() ) {
					$query->the_post();
					echo get_the_ID() . '___'.get_the_title( get_the_ID() ) . "\n\n";
				}
				*/
			}

		}

die;
return;
//			$gallery .= '[gallery size="medium" ids="';
//			$gallery .= '"]';

		$quotes = array(
			0 => array(
				'name'      => 'Ryan Hellyer',
				'questions' => array(
					0 => array(
						'question' => __( 'You started off well, but had lots of incidents. What happened to cause this?', 'undiecar' ),
						'answer'   => 'Some prick ran me off the road repeatedly. Total dick. Imma gonna kill em!',
					),
					1 => array(
						'question' => __( 'How do you feel about this track? Do you like it?', 'undiecar' ),
						'answer'   => 'It is like a gift from the gods to the racing drivers of the world! ... no, I fucking hate it.',
					),
				),
			),
			1 => array(
				'name'      => 'Nikolay Ladushkin',
				'questions' => array(
					0 => array(
						'question' => __( 'Two wins. Did you have any problems during the race?', 'undiecar' ),
						'answer'   => 'None. It is coz I am freakin awesome, unlike you and all those other useless bastards in the race!',
					),
				),
			),
		);

	/*
	 * QUESTIONS:
	 * 		torrid day, most incidents. What went wrong?
	 *		zero incidents: how did you do it?
	 *		least incidents: what's the trick to staying safe?
	 *		pole, both race wins: was it as easy as it looked?
	 *		fastest lap: what do you feel is the trick to gaining speed around here?
	 *		
	 *
	 *		general questions (could be asked of anyone):
	 *			do you like the car? Was the setup okay or should it be improved for next time?
	 *			other than yourself (obviously), which other driver do you think performed well at this event and why?
	 *			what aspects of this track do you like and dislike?
	 *			did you have any good battles during the races? And if so, with who?
	 *		
	 *		
	 *		bonus questions:
	 *			Where did the inspiration for your cars colour scheme come from?
	 *			Do you have any real world racing experience?
	 *			How do you find iRacing compares to other sims?
	 *			Do you follow any real world motor racing series? If so, which ones do you like most and why?
	 *			Which other Undieracer do you most enjoy competing against?
	 *			Other than sim racing, what other hobbies do you have?
	 *			What
	 */

		$qualifying = '2 laps';
		$race1 = array(
			'name' => 'Season 5: Daytona oval',
			'slug' => 'season-5-daytona-oval',
			'length' => '30 mins',
			'grid' => 'normal',
			'results' => array(
				'Nikolay Ladushkin',
				'Ryan Hellyer',
				'Matt Fretwell',
			),
		);
		$race2 = array(
			'name' => 'Season 5: Daytona oval',
			'slug' => 'season-5-daytona-oval',
			'length' => '30 mins',
			'grid' => 'reversed',
			'results' => array(
				'Matt Fretwell',
				'Nikolay Ladushkin',
				'Ryan Hellyer',
			),
		);
		$points = array(
			'Nikolay Ladushkin' => 22,
			'Matt Fretwell'     => 20,
			'Ryan Hellyer'      => 18,
		);

		$winner_name = array_keys( $points )[0];

		$track_link = '<a href="' . esc_url( get_permalink( $track_id ) ) . '">' . esc_html( get_the_title( $track_id ) ) . '</a>';
		$season_link = '<a href="' . esc_url( get_permalink( $season_id ) ) . '">' . sprintf( esc_html__( 'season %s', 'undiecar' ), $season ) . '</a>';
		$intro_options = array(
			sprintf( esc_html__( 'The recent round at %1s saw some exciting battles.', 'undiecar' ), $track_link ),
			sprintf( esc_html__( '%1s was the location for our most recent event in %2s of the Undiecar Championship.', 'undiecar' ), $track_link, $season_link ),
		);
		$intro = $intro_options[array_keys( $intro_options)[ rand( 0, ( count( $intro_options ) - 1 ) ) ]];

		$winner_link = '<a href="' . esc_url( home_url() . '/' . esc_html__( 'member', 'undiecar' ) . '/' . sanitize_title( $winner_name ) . '/' ) . '">' . esc_html( $winner_name ) . '</a>';
		$winner_options = array(
			sprintf( esc_html__( '%s took the honours and gained the most points across both races.', 'undiecar' ), $winner_link ),
			sprintf( esc_html__( '%s did an excellent job to come away with the biggest points increase for the round.', 'undiecar' ), $winner_link ),
		);
		$winner = $winner_options[array_keys( $winner_options)[ rand( 0, ( count( $winner_options ) - 1 ) ) ]];

	echo '
	<style>* {font-family: sans-serif;color:#000}a {color:#dd0000;}</style>


	<p>
		 ' . $intro . '
		 ' . $winner . '
	</p>

	<p>
		<strong><a href="' . esc_url( get_permalink( $event_id ) ) . '">' . esc_html__( 'See full results here', 'undiecar' ) . '</a></strong>
	</p>
	';

	if ( isset( $quotes ) ) {

		echo '<h3>' . esc_html__( 'Interviews', 'undiecar' ) . '</h3>';

		foreach ( $quotes as $key => $quote ) {

			$name = $quote['name'];
			$questions = $quote['questions'];

			foreach ( $questions as $key2 => $the_question ) {
				echo '
				<p>
					' . esc_html( $the_question['question'] ) . '
					<blockquote>' . esc_html( $name ) . ':</em> ' . esc_html( $the_question['answer'] ) . '</blockquote>
				</p>';
			}

		}

	}

	echo '

	<h3>' . esc_html__( 'Gallery', 'undiecar' ) . '</h3>

	' . do_shortcode( $gallery ) . '

	';

		die;
	}

	public function questionaire( $content ) {

return $content; // disabled until ready for prime time.

		$query = self::get_interview_query( get_the_ID() );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$content .= '
				<h3>' . esc_html__( 'Race interview', 'undiecar' ) . '</h3>
				<form action="" method="POST">';

				$content .= '				
					<p>
						<label>' . esc_html( $question ) . '</label>
					</p>';

				$content .= '
				</form>';
			}
			wp_reset_query();
		}

		return $content;
	}

}
