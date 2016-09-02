<?php
/**
 * The main template file.
 *
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */
get_header();


echo '<div style="
padding: 5px 30px;
border: 1px solid red;
background: #d9c0c0;
-moz-border-radius: 20px;
-webkit-border-radius: 20px;
border-radius: 20px;
font-weight: bold;
">

	<p>
		A hostile takeover occurred within a Facebook group I used to be involved with. 
		The group administration was run with a simple democratic approach.
		A single rogue admin tricked us into allowing another admin to join our group, and 
		just after we realised what had occurred, myself and other other elected admin were 
		unexpectedly removed without any consultation with the rest of the administration/moderation team. 
		All group moderators chose to leave within hours of this occurring.
	</p>
	<p>
		This website is a collection of the resources we had created for the group. I am keeping 
		them here in case they are useful to others. If the data becomes significantly out of 
		date, then I will remove the website.
	</p>
	<p>
		The original groups website content, used a 
		Creative Commons Attribution-ShareAlike 4.0 International License and it\'s website design 
		used GPL license version 2.0.
	</p>

</div>';


// Load main loop
if ( have_posts() ) {

	// Start of the Loop
	while ( have_posts() ) {
		the_post();
		echo '<article>';
		echo '<h1>';
		the_title();
		echo '</h1>';
		echo '<p id="updated">';
		echo sprintf( __( 'Last updated: %s', 'free-advice-berlin' ), get_the_modified_time( 'jS \of F Y' ) );
		if ( current_user_can( 'edit_pages' ) ) {
			echo '<a class="alignright" href="' . esc_url( get_edit_post_link() ) . '"><strong>Edit</strong></a>';
		}
		echo '</p>';
		the_content();
		echo '</article>';

		do_action( 'fab_after_content' );

		// If comments are open or we have at least one comment, load up the comment template
		if ( comments_open() ) {
			comments_template( '', true );
		}

	}
}

get_footer();
