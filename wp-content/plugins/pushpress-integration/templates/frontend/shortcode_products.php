<?php
	
	if( empty( $products ) ){
		?>

			<ul class="wp-pushpress-cat">
				<li class="wp-pushpress-item-other">
					<strong>No product available</strong>
				</li>
			</ul>
		<?php
	}else{
		foreach ($products as $keyItem => $item) {
			$linkTo = str_replace('{subdomain}', $this->subdomain, PUSHPRESS_CLIENT) . 'product/purchase/';
			if ( $keyItem == 'preorder_categories_id' ){
				$linkTo = str_replace('{subdomain}', $this->subdomain, PUSHPRESS_CLIENT) . 'product/preorder/';
			}

			?>
			<div class="wp-pushpress">

				<ul class="wp-pushpress-list">
					<?php if (!$atts['id']) { ?>
					<li class="item-first">
						<h3><?php echo $item['category_name'];?></h3>						
					</li>
					<?php } ?>
				<?php
				foreach ($item['products'] as $key => $value) {
					$n = count( $value['price'] );
					?>

					<li class="item-other">
						<div class="item-details">
							<span class="item-name"><?php echo $value['name'];?></span>
							<span class="item-price">
								<?php
									if ($n>1) {
										echo $client->_country->currency_symbol . number_format($value['price'][0], 2) . " - $" . number_format($value['price'][$n-1], 2);
									}else{
										echo $client->_country->currency_symbol . number_format($value['price'][0], 2);
									}
								?>
							</span>
						</div>
						<div class="item-button">
							<span class="wp-pushpress-id">
								<button data-href="<?php echo $linkTo . $key;?>" data-target="_blank">Buy</button>
							</span>
						</div>
					</li>
					<?php	
				}
				?>

				</ul>
			</div>
			<?php 
		}
	}