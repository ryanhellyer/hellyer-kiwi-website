<style>
input:invalid, select:invalid, textarea:invalid{ 
	border: 1px solid red;
}
input:valid, select:valid, textarea:valid{ 
	border: inherit;
}
</style>


	<div class="wp-pushpress">
		<ul class="wp-pushpress-list">
			<li class="item-first">
				<h3><?php echo $leads['lead_page_title'];?></h3>
				<div class="clear"></div>
			</li>
			<li class="item-other">
				<p class="lead-text"><?php echo $leads['lead_page_description'];?></p>
				<?php Wp_Pushpress_Messages::get_messages();?>

				<?php if (!isset($_POST['form_submitted'])) { ?>
				<form id="subscribe-form" class="form" action="" method="post">
				<div class="btn-group">
					<label for="billing_first_name">Your Name</label>
					<input required name="billing_first_name" id="billing_first_name" value="<?php echo $form['billing_first_name'];?>" placeholder="First Name" />
					<input required name="billing_last_name" id="billing_last_name" value="<?php echo $form['billing_last_name'];?>" placeholder="Last Name" />
				</div>
				<div class="btn-group">
					<label for="email">Your Email</label>
					<input name="email" id="email" value="<?php echo $form['email'];?>" placeholder="email@example.com" required />
				</div>
				<?php 
					if ($leads['lead_page_show_dob']) { 
				?>
				<div class="btn-group">
					<label for="your_birthday">Your Date of Birth</label>
					<input name="your_birthday" <?php if ($leads['lead_page_dob_required']) { echo 'required'; }?> id="your_birthday" class="your-birthday" value="<?php echo $form['your_birthday'];?>" placeholder="01/01/1990" />
				</div>
				<?php } // if show dob 
					if ($leads['lead_page_show_phone']) { 
				?>				
				<div class="btn-group">
					<label for="phone">Your Phone</label>
					<input name="phone" id="phone" <?php if ($leads['lead_page_phone_required']) { echo 'required'; }?> value="<?php echo $form['phone'];?>" placeholder="310-989-5552" />
				</div>
				<?php } // show phont
					if ($leads['lead_page_show_postal']) {
				?>
				<div class="btn-group">
					<label for="billing_postal_code">Your Postal Code</label>
					<input name="billing_postal_code" id="billing_postal_code" <?php if ($leads['lead_page_postal_required']) { echo 'required'; }?> value="<?php echo $form['billing_postal_code'];?>" placeholder="90000" />
				</div>
				<?php
					} // show postal
					if ($leads['lead_page_show_lead_types']) {
						if (isset($leadTypes) && count($leadTypes)) {
							if (count($leadTypes) == 1) {
								reset($leadTypes);
								$lead_type = key($leadTypes);
								$redirect = $leadTypes[$lead_type];

								?>
								<input type="hidden" name="lead_type" id="lead-type-nonce" value="<?php echo $lead_type;?>">
								<input type="hidden" name="redirect_nonce" id="redirect_nonce" value="<?php echo $redirect ?>">
								<?php
							}
							else {
								?>
								<div class="btn-group">
									<label for="objective">What Are You Interested In?</label>
									<select name="lead_type" id="lead-type" required>
										<option value="">-Select one-</option>
										<?php foreach ($leadTypes as $lead_type=>$redirect) {
										$selected = "";
										if ($form['lead_type'] === $lead_type) {
											$selected = "selected";
										}
										?>
										<option rel="<?php echo $redirect;?>" value="<?php echo $lead_type;?>" <?php echo $selected;?>><?php echo $lead_type;?>
											<?php
											}
											?>
									</select>
									<input type="hidden" name="redirect_nonce" id="redirect_nonce" value="<?php echo $form['redirect_nonce']?>">
								</div>
							<?php  } // if count == 1
						} // if is set types
					} // if show leads types
					?>
				<?php
					if ($leads['lead_page_show_client_objectives']) { 
				?>
				<div class="btn-group">
					<label for="objective">What are your objectives</label>
					<select name="objective" id="objective">
						<option value="">-Select one-</option>
						<?php for($i = 0; $i < sizeof($leadsObj); $i++):?>

							<option value="<?php echo $leadsObj[$i];?>"<?php echo ($leadsObj[$i] == $form['objective']) ? 'selected' : '';?>><?php echo $leadsObj[$i];?></option>
						<?php endfor;?>

					</select>
				</div>
				<?php 
					} // show objectives
					if ($leads['lead_page_show_preferred_communication']) {
				?>
				<div class="btn-group">
					<label for="preferred-communication">Preferred Communication</label>
					<select <?php if ($leads['lead_page_preferred_comm_required']) { echo 'required'; }?> name="preferred_communication" id="preferred-communication">
                        <option value="">-Select one-</option>
                        <option <?php if ( $form['preferred_communication'] == 'any' ) { echo 'selected'; }?> value="any">Any</option>
                        <option <?php if ( $form['preferred_communication'] == 'email' ) { echo 'selected'; }?> value="email">Email</option>
                        <option <?php if ( $form['preferred_communication'] == 'phone' ) { echo 'selected'; }?> value="call">Phone</option>
                        <option <?php if ( $form['preferred_communication'] == 'text' ) { echo 'selected'; }?> value="text">Text</option>
                    </select> 
				</div>
				<?php } //  show pref comm
					if ($leads['lead_page_show_referral_source']) { 
				?>
				<div class="btn-group">
					<label for="referred_by_id">How did you hear about us?</label>
					<select <?php if ($leads['lead_page_referral_required']) { echo 'required'; }?> name="referred_by_id" id="referred_by_id">
						<option value="">-Select one-</option>
						<?php for($i = 0; $i < sizeof($referral); $i++):?>
							<option open-staff-list="<?php echo $referral[$i]['show_staff_list'];?>"  value="<?php echo $referral[$i]['id'];?>"<?php echo ($referral[$i]['id'] == $form['referred_by_id']) ? 'selected' : '';?>><?php echo $referral[$i]['name'];?></option>							
						<?php endfor;?>
					</select>
				</div>
				<div id="referred-by-staff-container" class="btn-group" style="display:none;">
                     <select id="referred-by-user-id" name="referred_by_user_id">
                        <option value="">-- SELECT ONE --</option>
                        <?php 
                        foreach ($staff as $s) {
                            $selected = '';
                            if (isset($form['referred_by_user_id']) && ($s->uuid == $form['referred_by_user_id'])) { 
                            	$selected = 'selected';
                            }  
                           	echo '<option  ' .$selected. ' value="' .$s->uuid. '">'  . $s['first_name'] . ' ' . $s['last_name']. '</option>';
                        }
                        ?>
                    </select>      
                </div>                
				<?php } ?>
				<?php foreach ($client->sub_accounts as $sub_account) { 
                	if (strpos($sub_account->plan_id, "-addon-mlg" )) { 
                ?>
                <div class="btn-group">
                    <label for="">What Time Would You Like To Use The Gym?</label>
                    <div>
                        <div class="radio">
                            <label for="gym-time-am" >
                                <input type="radio" <?php if ($form['lead_desired_gymtime'] == "AM") { echo "checked"; } ?> name="lead_desired_gymtime" id="gym-time-am" value="AM"> AM (5am - 1pm)
                            </label>
                        </div>
                        <div class="radio">
                            <label for="gym-time-pm">
                                <input type="radio" <?php if ($form['lead_desired_gymtime'] == "PM") { echo "checked"; } ?> style="width:auto;" name="lead_desired_gymtime" id="gym-time-pm" value="PM" > PM (1pm - 9pm)                        
                            </label>
                        </div>
                    </div>                                    
                </div> 
            	<?php 
            			break;
            		} 
            	}
            	?>

				<?php 
				 if ($leads['lead_page_allow_message']) { ?>
						<div class="btn-group">
							<label for="billing_exp" class="col-xs-12">Questions? Send us a message along with this!</label>
							<textarea <?php if ($leads['lead_page_message_required']) { echo 'required'; }?> class="form-control" name="lead_message"><?php echo $form['lead_message'];?></textarea>
						</div>
					<?php } ?>
				<div class="btn-group">
					<?php wp_nonce_field( 'save_leads_info', 'save_leads_info_nonce');?>
					<button type="submit" name="btnLead">Submit</button>
				</div>
				</form>
				<?php } ?>
			</li>
		</ul>
	</div>