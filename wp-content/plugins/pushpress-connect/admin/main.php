<?php

$pagesID = get_option( 'wp-pushpress-page-id' );
if (
	isset( $_POST['save_pushpress_apikey_nonce'] )
	&&
	wp_verify_nonce( $_POST['save_pushpress_apikey_nonce'], 'save_pushpress_apikey' )
) {
	$arrayArgs = array(
		'products' => 'product-enabled',
		'plans'    => 'plan-enabled',
		'schedule' => 'schedule-enabled',
		'workouts' => 'workout-enabled',
		'leads'    => 'lead-enabled'
	);

	if ( isset( $_POST['btnIntegration'] ) ) {
		
		foreach ( $arrayArgs as $key => $arrayArg ) {

			// feature check box available
			if ( isset( $_POST[ $arrayArg ] ) && sanitize_text_field( $_POST[ $arrayArg ] ) == 'yes' ) {
				//enable feature option
				$arrayArg = wp_kses_post( $arrayArg );
				update_option( 'wp-pushpress-feature-' . $arrayArg, 'yes' );
				//update post 
				$post = array(
					'ID'          => $pagesID[$key],
					'post_status' => 'publish'
				);
				wp_update_post( $post );
			} else {
				//disable feature option
				update_option( 'wp-pushpress-feature-' . $arrayArg, 'no' );
				//update post 
				$post = array(
					'ID'          => $pagesID[$key],
					'post_status' => 'private'
				);
				wp_update_post( $post );
			}
		}
	}
}

$pushpress_pairing_code = get_option( 'wp-pushpress-pairing-code' );
$pushpress_secret_code = get_option( 'wp-pushpress-secret-code' );

$scheduleEnabled = get_option( 'wp-pushpress-feature-schedule-enabled' );
$workoutEnabled = get_option( 'wp-pushpress-feature-workout-enabled' );
$planEnabled = get_option( 'wp-pushpress-feature-plan-enabled' );
$productEnabled = get_option( 'wp-pushpress-feature-product-enabled' );
$leadEnabled = get_option( 'wp-pushpress-feature-lead-enabled' );

?>

<div class="wrap">
	<div class="dashboard-title">

		<img class="pushpress-logo" src="<?php echo esc_url( PUSHPRESS_URL . '/images/img_logo.png' );?>" alt="pushpress logo">

		<h1><?php esc_html_e( 'PushPress Integration', 'pushpress-connect' ); ?></h1>
		<?php
				if ( $client ) { ?><h3><?php echo esc_html( $client->company );?></h3><?php 
		} ?>		

	</div>
	<div class="clear"></div>

	<p>&nbsp;</p>

	<h2>
		<?php esc_html_e( 'Account Settings', 'pushpress-connect' ); ?>
	</h2>

	<?php Wp_Pushpress_Messages::get_messages(); ?>
	<p class="integration-desc">
		<?php printf( esc_html__( 'You need a PushPress Membership Management account to activate this plug-in. 
		If you don’t already have one you can sign-up for one %s.', 'pushpress-connect' ), '<a href="https://www.pushpress.com">here</a>' ); ?>
	</p>

	<p>&nbsp;</p>

	<h2><?php esc_html_e( 'PushPress Integration', 'pushpress-connect' ); ?></h2>
	<?php 
		if ( PUSHPRESS_INTEGRATED ) { ?>
		<div style="background:green; display:inline-block; padding:20px; color:white;">
			<strong><?php
				printf(
					esc_html__( 'PushPress is currently connected to %s', 'pushpress-connect' ),
					esc_html( $client->company )
				);
			?></strong>
		</div>

	<?php } ?>

	<form class="pp-form pp-form-vertical" method="post">
		
		<div class="pp-form-group">					
			<label for="pushpress_pairing_code"><?php esc_html_e( 'WordPress Pairing Token', 'pushpress-connect' ); ?></label>
			<div class="form-group">
				<input type="text" id="pushpress_pairing_code" name="pushpress_pairing_code" value="<?php echo esc_attr( $pushpress_pairing_code );?>" placeholder="0b1a3ab99c9f3f1a">
			</div>					
		</div>
		<div class="pp-form-group">
			<label for="pushpress_secret_code"><?php esc_html_e( 'Wordpress Secret Code', 'pushpress-connect' ); ?></label>
			<div class="form-group">
				<input type="text" id="pushpress_secret_code" name="pushpress_secret_code" value="<?php echo esc_attr( $pushpress_secret_code );?>" placeholder="key_aabbccddeeff11223344556677889900">
			</div>
			<p class="integration-link">
				<a href="https://help.pushpress.com/setup/connecting-pushpress-to-your-website/install-wordpress-plugin" target="_blank">
					<?php esc_html_e( 'Where do I find this information?', 'pushpress-connect' ); ?>
				</a>
			</p>
		</div>
		<div class="form-group">
			<?php wp_nonce_field( 'save_pushpress_apikey', 'save_pushpress_apikey_nonce' );?>
			<button class="btn" type="submit" name="btnAccount"><?php esc_html_e( 'Save', 'pushpress-connect' ); ?></button>
		</div>
		
	</form>

	<p>&nbsp;</p><!-- Crude spacer -->

	<h3><?php esc_html_e( 'Additional plugins', 'pushpress-connect' ); ?></h3>
	<p>
		<?php esc_html_e( 'Additional plugins can be downloaded from the following locations.', 'pushpress-connect' ); ?>
		<br />
		<a href="#">PushPress Schedule</a> | <a href="#">PushPress Lead Capture Form</a>
	</p>

</div>












<div>
	<h2>Short Codes</h2>
	<ul class="pp-list">
















<?php print_r( $opts ); die; ?>

	<?php foreach ($opts as $key=>$value) { ?>
		<li><?php echo  "[pushpress-shortcode name='" . $key . "'] => " . $value;?></li>
	<?php } ?>
</div>
<script type="text/javascript">
	var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

	elems.forEach(function(html) {
	  var switchery = new Switchery(html);
	});

	jQuery(document).ready(function($) {
		$('.js-switch').on('change', function(){
			var btnName = $(this).attr('name');
			var data = {
				'action': 'pushpress_ajax'
			};
			data[ btnName ] = 'no';
			if ( $(this).prop( "checked" ) ){
				data[ btnName ] = 'yes';
			}
			var me  = this;
			$.post(ajaxurl, data, function(response) {
				if ( response.result != 0){
					var $textStatus = $(me).closest('.integrations-check').find('.integration-status');
					if ( response.status == 'yes' ){
						$textStatus.text('Active');
						$textStatus.removeClass('disabled');
					}else{
						$textStatus.text('Disabled');
						$textStatus.addClass('disabled');
					}
				}
			}, 'json');

		});

		function remove_option(options){
			//var first = $(options + ":first").first();
			$(options).each(function(){
				//if( $(this).val() != first.val() ){
					$(this).remove();
				//}
			});
		}

		var section_shortcode = '.pushpress-wrap .container #help-container p .section-shortcode';
		var genarete_code = '.pushpress-wrap .container #help-container p .genarete-code';
		var loader_img = '.pushpress-wrap .container #help-container p .spinner';

		function hide_code(){
			if( $( section_shortcode ).val() == "" ){
				$( genarete_code ).css({
					'display':'none'
				});
			}else{
				$( genarete_code ).css({
					'display':'block'
				});
			}
		}

		hide_code();

		$( section_shortcode ).on( 'change', function(){
			var btnName = $(this).attr('name');
			var data = {
				'action': 'pushpress_ajax_section'
			};
			data[ btnName ] = $(this).val();
			var me  = this;

			$( genarete_code ).attr( 'disabled', 'disabled' );
			remove_option( genarete_code + ' option' );
			$( loader_img ).css( {'display':'inline-block'} );
			$.post(ajaxurl, data, function(response) {
				if ( response.result != 0 ){
					var value = "";
					var text = "";

					switch ( $(me).val() ) {
						case 'plans':
							text = "- Select Plan -";
							break;
						
						case 'events':
							text = "- Select Event -";
							break;

						case 'products':
							text = "- Select Product -";
							break;
					}
					$( genarete_code ).append( new Option( text, value ) );

					var n = response.length;
					for( i = 0; i < n; i++ ){
						$( genarete_code ).append( new Option( response[i].name, response[i].id  ) );
					}
				}
				$( genarete_code ).removeAttr( 'disabled' );
				$( loader_img ).removeAttr( 'style' );
				hide_code();
			}, 'json');
			
		} );

		$( genarete_code ).on( 'change', function(){
			var $section = $( section_shortcode );

			var $input = $( '.pushpress-wrap .container #help-container p #shortcode_output');
					
			var id = $( this ).val();

			switch ( $section.val() ) {
				case 'plans':

					$input.val( '[wp-pushpress-plans id="' + id + '"]' );

					break;
				
				case 'events':

					$input.val( '[wp-pushpress-plans id="' + id + '"]' );

					break;
				case 'products':
					
					$input.val( '[wp-pushpress-products category="' + id + '"]' );

					break;
			}
			$input.select();
			
		} );

	});
</script>
