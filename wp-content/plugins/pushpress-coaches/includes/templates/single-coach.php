<?php

get_header();


$show_default_title = get_post_meta( get_the_ID(), '_et_pb_show_title', true );

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );
$meta = get_post_meta(get_the_ID());

$og_content = $posts[0]->post_content;

$args = array(
    'post_type'=> 'coaches',
    'post_status'    => 'private',
    'order'    => 'ASC'
);           
$template = get_posts( $args );
$template = $template[0];
$posts[0]->post_content = $template->post_content;

$thumb = '';

$width = (int) 500;
$height = 500;
$classtext = 'et_featured_image';
$titletext = get_the_title();
$thumb = get_the_post_thumbnail_url( $post_id, 'medium' );         // Medium resolution
$post_format = et_pb_post_format();
?>

<div id="main-content" class="single-coach">

<?php while ( have_posts() ) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<div class="entry-content" style="display:none;">			
		<?php
			the_content();
		?>
		</div> <!-- .entry-content -->

		<div id="additional-content" style="display:none;">
			<h6>Additional Info</h6>
			<?php echo $og_content;?>
		</div>

	</article> <!-- .et_pb_post -->

<?php endwhile; ?>

</div> <!-- #main-content -->


<script>
<?php if (strlen(trim($thumb))) { ?>
	//console.log("YES HEADSHOT");
	jQuery('#pushpress-coach-headshot img').attr("src", "<?php echo $thumb;?>").css("width","100%");		
<?php } else { ?>
	//console.log("NO HEADSHOT");
	jQuery('#pushpress-coach-headshot').css("display","none");
<?php } ?>

<?php if (!strlen(trim($meta['coach_turning_point'][0]))) { ?>
	jQuery('#pushpress-coach-turning-point-container').css("display","none");
<?php } ?>
<?php if (!strlen(trim($meta['coach_feats_of_strength'][0]))) { ?>
	jQuery('#pushpress-coach-feats-of-strength-container').css("display","none");
<?php } ?>
<?php if (!strlen(trim($meta['coach_motivation'][0]))) { ?>
	jQuery('#pushpress-coach-motivation-container').css("display","none");
<?php } ?>
<?php if (!strlen(trim($meta['coach_qualifications'][0]))) { ?>
	jQuery('#pushpress-coach-qualifications-container').css("display","none");
<?php } ?>
<?php if (!strlen(trim($meta['coach_title'][0]))) { ?>
	jQuery('#pushpress-coach-title-container').css("display","none");
<?php } ?>
<?php if (!strlen(trim($meta['coach_about'][0]))) { ?>
	jQuery('#pushpress-coach-about-container').css("display","none");
<?php } ?>

<?php if (!strlen(trim($meta['coach_facebook'][0]))) { ?>
	jQuery('#pushpress-coach-facebook-container').css("display","none");
<?php } ?>
<?php if (!strlen(trim($meta['coach_instagram'][0]))) { ?>
	jQuery('#pushpress-coach-instagram-container').css("display","none");
<?php } ?>
<?php if (!strlen(trim($meta['coach_website'][0]))) { ?>
	jQuery('#pushpress-coach-website-container').css("display","none");
<?php } ?>
<?php if (!strlen(trim($meta['coach_email'][0]))) { ?>
	jQuery('#pushpress-coach-email-container').css("display","none");
<?php } ?>
<?php if (!strlen(trim($meta['coach_phone'][0]))) { ?>
	jQuery('#pushpress-coach-phone-container').css("display","none");
<?php } ?>

<?php if (strlen(trim($og_content))) { ?>
	var additional_content = jQuery('#additional-content').html();
	
	jQuery('#pushpress-coach-additional-content-container').html(additional_content).css("display","block");	
<?php } ?>


jQuery('.entry-content').css("display", "block");

</script>

<?php get_footer(); ?>