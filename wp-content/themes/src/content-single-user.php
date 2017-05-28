<?php



/**
 * Single User Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bbp_template_notices' ); ?>




<style>
#members-car {
	float: right;
	width: 500px;
}

h1 img {
	margin-right: 15px;
	width: 128px;
	height: 128px;
	border-radius: 64px;
	position: relative;
	top: 50px;
}

</style>

<?php do_action( 'bbp_template_before_user_details' ); ?>

<h1>
	<?php echo get_avatar( bbp_get_displayed_user_field( 'user_email', 'raw' ), apply_filters( 'bbp_single_user_details_avatar_size', 500 ) ); ?>

	Ryan Hellyer<?php

	if ( bbp_is_user_home() || current_user_can( 'edit_users' ) ) { ?>
	<small>(<a href="<?php bbp_user_profile_edit_url(); ?>" title="<?php printf( esc_attr__( "Edit %s's Profile", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php _e( 'Edit', 'bbpress' ); ?></a>)</small><?php
	}

	?>
</h1>


<div id="members-car">
	<?php
	$image = get_user_meta( bbp_get_displayed_user_id(), 'bbpress_secondary_image', true );
	if ( isset( $image['full'] ) ) {
		echo '<img src="' . esc_url( $image['full'] ) . '" />';
	}
 	?>
</div><!-- #avatar -->


<?php do_action( 'bbp_template_after_user_details' ); ?>




<?php if ( bbp_is_single_user_edit()          ) bbp_get_template_part( 'form', 'user-edit'       ); ?>
<?php if ( bbp_is_single_user_profile()       ) {

	do_action( 'bbp_template_before_user_profile' ); ?>

	<?php if ( bbp_get_displayed_user_field( 'description' ) ) : ?>

		<p class="bbp-user-description"><?php bbp_displayed_user_field( 'description' ); ?></p>

	<?php endif; ?>

<strong>RESULTS AND SHIT WILL BE POSTED HERE</strong>

	<p class="bbp-user-topic-count"><?php printf( __( 'Topics Started: %s',  'bbpress' ), bbp_get_user_topic_count_raw() ); ?></p>
	<p class="bbp-user-reply-count"><?php printf( __( 'Replies Created: %s', 'bbpress' ), bbp_get_user_reply_count_raw() ); ?></p>

	<?php do_action( 'bbp_template_after_user_profile' );

}
 ?>
