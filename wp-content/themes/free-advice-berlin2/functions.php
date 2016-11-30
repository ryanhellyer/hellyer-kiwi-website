<?php

function fuck_you_tanzia() {
echo '



<!-- PM me if you want to know the details behind what happened ;) -->
<div style="
padding: 5px 30px;
border: 1px solid red;
background: #d9c0c0;
-moz-border-radius: 20px;
-webkit-border-radius: 20px;
border-radius: 20px;
font-weight: bold;
">

	<p>
		A Facebook group I used to be an administrator for was run with a simple democratic approach.
		A rogue admin tricked us into allowing another admin to join our group, and 
		just after we realised what had occurred, myself and the other elected admin were 
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
	<p>
		Note: You may have been redirected here from the original groups domain name. I do not control that domain.
		The owner of that domain is also no longer involved with the original Facebook group.
	</p>

</div>



';
}

/**
 * Autoload the classes.
 * Includes the classes, and automatically instantiates them via spl_autoload_register().
 *
 * @param  string  $class  The class being instantiated
 */
function autoload_free_advice_berlin( $class ) {

	// Bail out if not loading a Media Manager class
	if ( 'Free_Advice_Berlin_' != substr( $class, 0, 19 ) ) {
		return;
	}

	// Convert from the class name, to the classes file name
	$file_data = strtolower( $class );
	$file_data = str_replace( '_', '-', $file_data );
	$file_name = 'class-' . $file_data . '.php';

	// Get the classes file path
	$dir = dirname( __FILE__ );
	$path = $dir . '/inc/' . $file_name;

	// Include the class (spl_autoload_register will automatically instantiate it for us)
	require( $path );
}
spl_autoload_register( 'autoload_free_advice_berlin' );

new Free_Advice_Berlin_Admin;
new Free_Advice_Berlin_Setup;
new Free_Advice_Berlin_Ratings;
new Free_Advice_Berlin_Related_Group_Posts;
new Free_Advice_Berlin_Legal_Notice;
new Free_Advice_Berlin_Facebook;
new Free_Advice_Berlin_Show;
new Free_Advice_Berlin_Number_of_Members;
new Free_Advice_Berlin_Facebook_Opengraph;

/**
 * Output a search form.
 */
function free_advice_berlin_search_form() {
	echo '

		<form id="search-form" method="get" action="' . esc_url( home_url() ) . '">
			<label for="search">Search</label>
			<input type="search" id="search" name="s" placeholder="Search" value="' . esc_attr( get_query_var( 's' ) ) . '" />
			<input type="submit" placeholder="Search ..." name="submit" value="Search" />
		</form>';
}
