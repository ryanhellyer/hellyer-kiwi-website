<?php
/**
* Template variables in scope:
* @var WP_User               $subscriber
* @var Prompt_Interface_Subscribable   $object        The thing being subscribed to
* @var WP_Post $subscribed_introduction Custom introduction content.
* @var array                 $comments      Comments so far for post subscriptions
*/
?>
<?php echo Prompt_Html_To_Markdown::h1( sprintf( __( 'Welcome, %s.', 'Postmatic' ), $subscriber->display_name ) ); ?>

<?php echo strip_tags( $object->subscription_description() ); ?>


<?php echo Prompt_Html_To_Markdown::h2( __( "What's next?", 'Postmatic' ) ); ?>

<?php
printf(
	__( 'Keep an eye on your inbox for content from %s.', 'Postmatic' ),
	strip_tags( $object->subscription_object_label() )
);
?>

<?php
if ( $object instanceof Prompt_Site or $object instanceof Prompt_User ) :
	echo  Prompt_Html_To_Markdown::convert( $subscribed_introduction );
elseif ( $comments ) :
	_e( 'The conversation so far is included below.', 'Postmatic' );
endif;
?> 


<?php if ( $comments ) : ?>

<?php echo Prompt_Html_To_Markdown::h2( __( "Here is the discussion so far", 'Postmatic' ) ); ?>

<?php
wp_list_comments( array(
	'callback' => array( 'Prompt_Email_Comment_Rendering', 'render_text' ),
	'end-callback' => '__return_empty_string',
	'style' => 'div',
), $comments );
?>

<?php _e( '* To leave a comment simply reply to this email. *', 'Postmatic' ); ?>

<?php
printf(
	__(
		'Please note: Your reply will be published on %s.',
		'Postmatic'
	),
	get_bloginfo( 'name' )
);
?>
<?php endif; ?>

