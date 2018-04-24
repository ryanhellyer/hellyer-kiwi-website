
<?php Wp_Pushpress_Messages::get_messages();?>

<?php if (!$_POST['form_submitted']) { ?>
<form action="" method="post" class="pp-form pp-form-vertical">
	<div class="pp-form-group">
		<label for="first_name"><?php esc_html_e( 'Your Name', 'pushpress-lead-form' ); ?></label>
		<input required="required" type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $post['first_name'] );?>" placeholder="<?php esc_html_e( 'First Name', 'pushpress-lead-form' ); ?>">
		<input required="required" type="text" name="last_name" id="last_name" value="<?php echo esc_attr( $post['last_name'] );?>" placeholder="<?php esc_html_e( 'Last Name', 'pushpress-lead-form' ); ?>">
	</div>

	<div class="pp-form-group">
		<label for="email"><?php esc_html_e( 'Your Email', 'pushpress-lead-form' ); ?></label>
		<input required="required" type="email" name="email" id="email" value="<?php echo esc_attr( $post['email'] );?>" placeholder="you@youremail.com">
	</div>

	<?php if ($integration_settings['lead_page_show_dob']) { ?>
	<div class="pp-form-group">
		<label for="dob"><?php esc_html_e( 'Your Date of Birth', 'pushpress-lead-form' ); ?></label>
		<select <?php if ( $integration_settings['lead_page_dob_required'] ) { echo 'required="required"'; }?>" name="dob_year" id="dob-year" style="width:25%; display:inline;">
			<option value="">YEAR</option>
			<?php 
				$start_year = date("Y");
				$end_year = $start_year - 100;

				for($year = date("Y"); $year > $end_year; $year--) { 
					$selected = "";
					if ($year == $post['dob_year']) { 
						$selected = "selected";
					}
					echo '<option value="' . $year . '" ' . $selected . '>'. $year . '</option>';
				} 
			?>
		</select>
		<select <?php if ($integration_settings['lead_page_dob_required']) { echo 'required="required"'; }?>" name="dob_month" id="dob-month" style="width:50%; display:inline;">
			<option value="">MONTH</option>
			<?php 
			for($month = 1; $month < 13; $month++) { 
				$selected = "";
				if ($month == $post['dob_month']) { 
					$selected = "selected";
				}
				echo '<option value="' . esc_attr( $month ) . '" ' . $selected . '>' . esc_html( date( "F", strtotime( "$month/1/2000" ) ) ) . '</option>';
			}
			?>
		</select>
		<select <?php if ( $integration_settings['lead_page_dob_required'] ) { echo 'required="required"'; } ?>" name="dob_day" id="dob-day" style="width:22%; display:inline;">
			<option value="">DAY</option>
			<?php 
				for($day = 1; $day < 32; $day++) { 
					$selected = "";
					if ($day == $post['dob_day']) { 
						$selected = "selected";
					}
			?>
				<option <?php echo $selected;?> class="<?php if ( $day > 28 ) { echo ' days-30'; } if ( $day > 30 ) { echo ' days-31'; } ?>" value="<?php echo esc_attr( $day );?>"><?php echo esc_html( $day ); ?></option>
			<?php } 
			?>
		</select>
	</div>
	<?php } ?>

	<?php if ($integration_settings['lead_page_show_phone']) { ?>
	<div class="pp-form-group">
		<label for="phone"><?php esc_html_e( 'Your Phone', 'pushpress-lead-form' ); ?></label>
		<input <?php if ( $integration_settings['lead_page_phone_required'] ) { echo 'required="required"'; }?>" name="phone" id="phone" value="<?php echo esc_attr( $post['phone'] ) ;?>" placeholder="555-890-1234">
	</div>
	<?php } ?>

	<?php if ($integration_settings['lead_page_show_postal']) { ?>
	<div class="pp-form-group">
		<label for="postal_code"><?php esc_html_e( 'Your Postal Code', 'pushpress-lead-form' ); ?></label>
		<input <?php if ($integration_settings['lead_page_postal_required']) { echo 'required="required"'; }?>" name="postal_code" id="postal_code" value="<?php echo esc_attr( $post['postal_code'] ); ?>" placeholder="01234">
	</div>
	<?php } ?>

	<?php if ($integration_settings['lead_page_show_client_objectives']) { ?>
	<div class="pp-form-group">
		<label for="billing_first_name"><?php esc_html_e( 'Your Objectives', 'pushpress-lead-form' ); ?></label>
		<select name="objective" id="objective">
			<option value=""> -- Select One --</option>
			<?php foreach ( $objectives as $objective ) { 
				$selected = "";
				if ( $objective == $post['objective'] ) { 
					$selected = "selected";
				}
			?>
				<option <?php echo $selected;?> value="<?php echo $objective;?>"><?php echo $objective;?></option>
			<?php } ?>
		</select>
	</div>
	<?php } ?>

	<?php if ($integration_settings['lead_page_show_preferred_communication']) {
				?>
				<div class="pp-form-group">
					<label for="preferred-communication">Preferred Communication</label>
					<select <?php if ($integration_settings['lead_page_preferred_comm_required']) { echo 'required'; }?> name="preferred_communication" id="preferred-communication">
						<option value="">-Select one-</option>
						<option <?php if ( $post['preferred_communication'] == 'any' ) { echo 'selected'; }?> value="any"><?php esc_html_e( 'Any', 'pushpress-lead-form' ); ?></option>
						<option <?php if ( $post['preferred_communication'] == 'email' ) { echo 'selected'; }?> value="email"><?php esc_html_e( 'Email', 'pushpress-lead-form' ); ?></option>
						<option <?php if ( $post['preferred_communication'] == 'phone' ) { echo 'selected'; }?> value="call"><?php esc_html_e( 'Phone', 'pushpress-lead-form' ); ?></option>
						<option <?php if ( $post['preferred_communication'] == 'text' ) { echo 'selected'; }?> value="text"><?php esc_html_e( 'Text', 'pushpress-lead-form' ); ?></option>
					</select> 
				</div>
	<?php } //  show pref comm ?>
	
	<?php                   
		if ($integration_settings['lead_page_show_lead_types']) {
			if (isset($lead_types) && count($lead_types)) {
				if (count($lead_types) == 1) {
					reset($lead_types);
					$lead_type = key($lead_types);
					$redirect = $lead_types->lead_type;

	?>
			<input type="hidden" name="lead_type" id="lead-type-nonce" value="<?php echo esc_attr( $lead_type ); ?>">
			<input type="hidden" name="redirect_nonce" id="redirect_nonce" value="<?php echo esc_attr( $redirect ); ?>">
	<?php
				}  // if count is 1
				else {
	?>
					<div class="pp-form-group">
						<label for="lead_type">What Are You Interested In?</label>
						<select name="lead_type" id="lead_type">
							<option value=""> -- Select One --</option>
							<?php foreach ($lead_types as $key=>$value) { 
								$selected = "";
								if (strtolower($post['lead_type']) == strtolower($key)) { 
									$selected = "selected";
								}
							?>
								<option rel="<?php echo esc_attr( $value ); ?>" <?php echo $selected;?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $key ); ?></option>
							<?php } ?>
						</select>
						<input type="hidden" name="redirect_nonce" id="redirect_nonce" value="<?php echo esc_attr( $form['redirect_nonce'] ); ?>">
					</div>
	<?php       } // if count == 1
			} // if is set types
		} // if show leads types
	?>

	<?php
		if ($integration_settings['lead_page_show_referral_source']) { 
	?>
	<div class="pp-form-group">
		<label for="referred_by_id">How did you hear about us?</label>
		<select <?php if ($integration_settings['lead_page_referral_required']) { echo 'required'; }?> name="referred_by_id" id="referred_by_id">
			<option value="">-Select one-</option>
			<?php foreach($referral_sources as $referral) { ?>
				<option open-staff-list="<?php echo esc_attr( $referral['show_staff_list'] ); ?>" 
					value="<?php echo esc_attr( $referral['id'] ); ?>" 
					<?php echo ($referral['id'] == $post['referred_by_id']) ? 'selected' : '';?>
				>
					<?php echo esc_html( $referral->name ); ?>
				</option>                            
			<?php } ?>
		</select>
	</div>
	<div id="referred-by-staff-container" class="pp-form-group" style="display:none;">
		 <select id="referred-by-user-id" name="referred_by_user_id">
			<option value="">-- SELECT ONE --</option>
			<?php 
			foreach ($staff as $s) {
				$selected = '';
				if (isset($post['referred_by_user_id']) && ($s->uuid == $post['referred_by_user_id'])) { 
					$selected = 'selected';
				}  
				echo '<option  ' .$selected. ' value="' . esc_attr( $s->uuid ) . '">'  . esc_html( $s['first_name'] . ' ' . $s['last_name'] ) . '</option>';
			}
			?>
		</select>      
	</div>                
	<?php } ?>

	<?php foreach ($client->sub_accounts as $sub_account) { 
		if (strpos($sub_account->plan_id, "-addon-pcp" )) { 
	?>
	<div class="pp-form-group">
		<label for=""><?php esc_html_e( 'What Time Would You Like To Use The Gym?', 'pushpress-lead-form' ); ?></label>
		<div>
			<div class="radio">
				<label for="gym-time-am" >
					<input type="radio" <?php if ($post['lead_desired_gymtime'] == "AM") { echo "checked"; } ?> name="lead_desired_gymtime" id="gym-time-am" value="AM"> AM (5am - 1pm)
				</label>
			</div>
			<div class="radio">
				<label for="gym-time-pm">
					<input type="radio" <?php if ($post['lead_desired_gymtime'] == "PM") { echo "checked"; } ?> style="width:auto;" name="lead_desired_gymtime" id="gym-time-pm" value="PM" > PM (1pm - 9pm)                        
				</label>
			</div>
		</div>                                    
	</div> 
	<?php 
			break;
		} 
	}
	?>


	<?php if ($integration_settings['lead_page_allow_message']) { ?>
	<div class="pp-form-group">
		<label for="billing_first_name"><?php esc_html_e( 'Want To Leave Us A Note?', 'pushpress-lead-form' ); ?></label>
		<textarea <?php if ($integration_settings['lead_page_message_required']) { echo 'required'; }?> name="message" id="message"><?php echo $post['message'];?></textarea>
	</div>
	<?php } 
	?>

	<div class="pp-form-group">
		<?php wp_nonce_field( 'lead_form_submission', 'lead_form_submission_nonce');?>
		<button class="btn"><?php esc_html_e( 'Submit', 'pushpress-lead-form' ); ?></button>
	</div>
</form>

<script>
	function validate_field(field) { 
		console.log(field);
		console.log(field.value);

		if (field.value.length) { 
			jQuery(field).addClass("valid").removeClass("invalid");
		}
		else { 
			jQuery(field).addClass("invalid").removeClass("valid");
		}
	}
	
	jQuery('input[required]').change(function() { 
		validate_field(this);        
	});
	jQuery('select[required]').change(function() { 
		validate_field(this);        
	});
	jQuery('textarea[required]').change(function() { 
		validate_field(this);        
	});

	var rel = jQuery('#lead_type').find('option:selected').attr('rel');
	if (rel != undefined) {
		jQuery('#redirect_nonce').val(rel);
	}
	jQuery('#lead_type').on('change', function () {
		console.log("changed lead type");
		rel = jQuery('option:selected', this).attr('rel');
		jQuery('#redirect_nonce').val(rel);
	});

	jQuery('#referred_by_id').change(function () {
		console.log("Changed referred by id");

		var selected = jQuery('#' + this.id + ' option:selected');
		var show_staff = parseInt(selected.attr("open-staff-list"));

		if (show_staff) {
			jQuery('#referred-by-staff-container').fadeIn();
		}
		else {
			jQuery('#referred-by-staff-container').fadeOut();
		}
	});


	// onload run validates
	jQuery('[required]').each(function() { 
		console.log("working on required field");
		console.log(this);
		validate_field(this);
	})
 </script>
 <?php }  // if posted ?>