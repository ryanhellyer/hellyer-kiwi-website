<?php
	
	$linkTo = str_replace('{subdomain}', $this->subdomain, PUSHPRESS_CLIENT) . 'plan/subscribe/';
	$planType = array('R' => 'Recurring', 'N' => 'Non-Recurring', 'P' => 'Punchcards');	
?>
	
	<div class="wp-pushpress">

	<?php
		$client = Pushpress_Client::retrieve('self');
        
		if( !empty( $plans ) && count($plans) > 0 ){
			foreach ($plans as $k => $item) {
	?>

		<ul class="wp-pushpress-list">
			<?php if (!$atts['id']) { ?>
			<li class="item-first">
				<h3><?php echo $planType[$k];?></h3>			
			</li>
			<?php
				}
				foreach ($item as $key => $value) {
			?>

			<li class="item-other">
				<div class="item-details">
					<span class="item-name"><?php echo $value['name'];?></span>
					<span class="item-price">
						<?php echo $client->_country->currency_symbol . number_format($value['price'], 2);?>
					</span>
				</div>				
				<span class="item-button">
					<button data-href="<?php echo $linkTo . $key;?>" data-target="_blank">More info & Sign-up</button>
				</span>						
			</li>
			<?php	
				}
			?>

		</ul>
	<?php
			}
		}
			$linkTo = str_replace('{subdomain}', $this->subdomain, PUSHPRESS_CLIENT) . 'event/register/';
			if( !empty($courses) && count($courses) > 0 ){
	?>

		<ul class="wp-pushpress-list">
			<li class="item-first"><h3>Course</h3>
				<div class="clear"></div></li>
			<?php
				foreach ($courses as $key => $value) {
			?>

			<li class="item-other">
				<div class="item-details">
					<span class="item-name"><?php echo $value['title'];?></span>
					<span class="item-price">
						<?php echo "$" . number_format($value['price'], 2);?>
					</span>
					<span class="item-date">
						<?php echo $value['start_datetime'];?>
					</span>
				</div>
				<span class="item-button">
					<button data-href="<?php echo $linkTo . $key;?>" data-target="_blank">More info & Sign-up</a>
				</span>				
			</li>
			<?php	
				}
			?>

		</ul>
		<?php
			}			
			if( !empty($events) && count($events) > 0 ){
		?>

		<ul class="wp-pushpress-list">
			<?php if (!$atts['id']) { ?>
			<li class="item-first">
				<h3>Events</h3>
			</li>
			
			<?php
			}	
				foreach ($events as $key => $value) {
			?>

			<li class="item-other">
				<div class="item-details">
					<span class="item-name"><?php echo $value['title'];?></span>
					<span class="item-price">
						<?php echo "$" . number_format($value['price'], 2);?>
					</span>
					<span class="item-date">
						<?php echo $value['start_datetime'];?>
						<?php if ( !empty( $value['end_datetime'] ) ): ?>
							<?php echo ' - ' . $value['end_datetime'];?>
						<?php endif;?>
					</span>
				</div>
				<span class="item-button">
					<button data-href="<?php echo $linkTo . $key;?>" data-target="_blank">More info & Sign-up</button>
				</span>
				<div class="clear"></div>
			</li>
			<?php	
				}
			?>

		</ul>
		<?php
			}
		?>
	</div>