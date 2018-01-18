<?php

// Bail out if not processing users now
if ( ! isset( $_GET['auto_news'] ) ) {
	return;
}

add_action( 'template_redirect', 'auto_news' );
function auto_news() {

	$track_id = 254;
	$season_id = '?';
	$season = '5';
	$event_id = 100;

	$args = array(
		'post_parent'            => $event_id,
		'posts_per_page'         => 500,
		'post_type'              => 'attachment',
		'post_status'            => 'inherit',
		'post_mime_type'         => 'image',
		'meta_key'               => 'gallery',
		'no_found_rows'          => true,  // useful when pagination is not needed.
		'update_post_meta_cache' => false, // useful when post meta will not be utilized.
		'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
		'fields'                 => 'ids'
	);
	$query = new WP_Query( $args );
	$gallery = '';
	if ( $query->have_posts() ) {
		$gallery .= '[gallery size="medium" ids="';
		while ( $query->have_posts() ) {
			$query->the_post();

			$gallery .= get_the_ID() . ',';
		}
		wp_reset_query();
		$gallery .= '"]';
	}

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
