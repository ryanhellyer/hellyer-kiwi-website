<?php	get_header(); ?>
		<div id="sidebar">
			<?php	get_sidebar(); ?>
		</div>
		<div id="maincontent">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<p id="postdate">
				<?php if (is_single()) { ?>Posted on<?php } if (is_page()) { ?>Updated on<?php } ?>
				<?php if (is_single() || is_page()) {the_modified_time('M jS Y'); } ?>
				<?php if (is_single()) { ?> by <?php the_author();} ?>
			</p>
			<?php $post = $posts[0];
			if (is_category()) { ?><h1>Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h1><?php }
			elseif( is_tag() ) { ?><h1>Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h1><?php }
			elseif (is_day()) { ?><h1>Archive for <?php the_time('F jS, Y'); ?></h1><?php }
			elseif (is_month()) { ?><h1>Archive for <?php the_time('F, Y'); ?></h1><?php }
			elseif (is_year()) { ?><h1>Archive for <?php the_time('Y'); ?></h1><?php }
			elseif (is_author()) { ?><h1>Author Archive</h1><?php }
			elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?><h1>Blog Archives</h1><?php }
			elseif (is_page() || is_single()) { ?><h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h1><?php }

			the_content('<br /><br />Continue Reading &raquo;');

			if (is_single()) { ?><p id="postinfo">Posted in <?php the_category(', ') ?>. <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?><?php edit_post_link('Edit', ' | ', ''); ?></p><?php } ?>
			<?php endwhile; ?>
			<p><strong><?php posts_nav_link(' - ','&#171; Prev','Next &#187;') ?></strong></p>
			<?php comments_template(); ?>
			<?php else : ?>
			<h2>Not Found</h2>
			<p>Sorry, but you are looking for something that isn't here.</p>
			<?php endif; ?>
		</div>
<?php	get_footer(); ?>
