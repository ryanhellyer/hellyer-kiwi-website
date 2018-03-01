<?php

$args = array(
    'post_type'         => 'coaches',
    'post_status'		=> 'publish',
    'posts_per_page'    => -1,
    'orderby'   		=> 'meta_value_num',
	'meta_key'  		=> 'coach_sort_order',
    'order'             => 'ASC',
    // This line ignores plugin ordering.
    'suppress_filters'  => true,
    
);

// query
$wp_query = new WP_Query( $args );


get_header();

?>

<style>
	#coaches-list-container { 
		overflow:hidden;	
	 }
	 h4 { 
	 	text-transform: uppercase;
	  }
	article { 
		float:left;
		width:50%;
		display:inline-block;
	 }
	article .coach-headshot { 
		width:50%;
		float:left;
		height:100%;
		display:inline-block;
	 }
	article .entry-content {
		width:50%;
		float:left;
		display:inline-block;
		background-position: center;
    	background-size: cover;
    	position:relative;
    	vertical-align:baseline;
	}
	article .coach-description { 
		padding-bottom:20px;
	 }

	article .coach-description h2 { 
		text-transform: uppercase !important;
	    font-size: 40px;
	    letter-spacing: 1px !important;
	    line-height: 1.1em !important;

	 }

	article .et_pb_promo_button:after { 
		color: #363636;
    	line-height: 1.7em;
    	font-size: 15px!important;
    	opacity: 0;
    	margin-left: -1em;
    	left: auto;
    	display: inline-block;
	 }

	@media (min-width: 981px)  {
		article .entry-content { 
			padding:3% 5%;
		 }
		article .coach-description p { 
			line-height:1.4em;
			font-size:16px;
		 }
	}

	@media only screen and (min-width: 1024px) and (max-width: 1280px) {
		article .coach-description h2 { 
		    font-size: 36px;			    
		 }
		 .cta_custom {
	    	top: 10px !important;
	    	position: relative;
		}
		article .coach-description { 
			padding-bottom:10px;
		 }
	}

	/******* Mobile Device *******/
	@media only screen and (max-width: 1023px) {
		article .entry-content { 
			padding:3%;
		 }

		.cta_custom {
	    	top: 5px !important;
	    	position: relative;
		}
		article .coach-description h2 { 
		    font-size: 32px;			    
		 }

		article .coach-description { 
			padding-bottom:10px;
		 }
	}

	@media (max-width: 980px) {
		article {
		    width:100%;
		 }
	}

	@media (max-width: 479px) {
		article .coach-headshot { 
			width: 100%!important;
    		margin: 0 0 30px 0;
		}		
		article .entry-content  { 
			width: 100%;
			padding:40px;
		 }
	}


</style>

<div id="main-content">
	<div id="coaches-header">	
		<?php  echo do_shortcode('[et_pb_section global_module="COACH_ARCHIVE_HEADER"][/et_pb_section]'); ?>
	</div>
	<div id="coaches-list-container">

<?php 
	$count = 0;
	$img_float_right = 0;
?>
<?php while ( $wp_query->have_posts() ) : the_post(); 
	
?>

	<?php if(get_post_status() == "publish") { ?>

		<?php 
			$default_image = "http://template.sites.pushpress.com/wp-content/uploads/sites/24/2017/08/grey-placeholder-5x5.gif";
			$post_id = get_the_ID();
			$count++;
			$coach_name = get_the_title();
			$meta = get_post_meta(get_the_ID());
			$thumb = '';

			$width = (int) 500;
			$height = 500;
			$thumb = get_the_post_thumbnail_url( $post_id, 'large' );         // Medium resolution

			if (!strlen(trim($thumb))) { 
				$thumb = $default_image;
			}

			$intro = $meta['coach_about'][0];
			if (strlen(trim($meta['coach_intro'][0]))) { 
				$intro = $meta['coach_intro'][0];
			}

			$needs_trunc = (strlen($intro) > 140) ? true : false;
			if ($needs_trunc) { 
				$intro = substr($intro, 0, 150);
				$trunc = strrpos ($intro, " ");
				$intro = substr($intro,0,$trunc) . '...';
			}			
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="min-height:400px;">
			<div class="et_pb_module et-waypoint et_pb_image et_pb_animation_left et_pb_image_0 et_always_center_on_mobile et_pb_image_sticky et-animated coach-headshot" <?php if ($img_float_right) { echo 'style="float:right;"'; }?>>
				<img src="<?php echo $thumb;?>" style="width:100%; height:auto;">
			</div>
			<div class="entry-content et_pb_column_1 et_pb_promo et-waypoint bottom-animated et_pb_column_1 et-animated">			
				<div class=" et_pb_module et_pb_text_align_left cta_custom et_pb_cta_0 et_pb_no_bg">
					<div class="coach-description">
						<h2><?php echo $coach_name;?></h2>			
						<p><?php echo $intro;?></p>
					</div>
					<?php echo '<a class=" et_pb_promo_button et_pb_button et_pb_custom_button_icon" style="letter-spacing:2px; font-weight:bold; text-transform:uppercase; border:0; padding:0; font-size:15px;" data-icon="$" href="/coaches/' . $post->post_name . '">Learn More</a>';
					?>					
				</div>
			</div>
			
		

	<?php 
		// check for float flip
		if ( ($count % 2) == 0) { 
			$img_float_right = ($img_float_right) ? 0 : 1;			
		}		
	} ?>

	</article> <!-- .et_pb_post -->

<?php endwhile; ?>

	</div> <!-- coaches-list-container -->

</div> <!-- #main-content -->
<?php get_footer(); ?>