<ul id="suckerfishnav"><?php wp_list_pages( 'title_li=' ); ?></ul>



			<?php
				if ( !function_exists('dynamic_sidebar')
				|| !dynamic_sidebar() ) :
			?>
			<h3>Categories</h3>
			<ul>
				<?php wp_list_categories('title_li='); ?>
			</ul>
			<h3>Pages</h3>
			<ul>
				<?php wp_list_pages('title_li=')?>
 	  	</ul>
			<h3>Recent Posts</h3>
			<ul>
				<?php $myposts = get_posts('numberposts=10');foreach($myposts as $post) :?>
				<li><a href="<?php the_permalink(); ?>"><?php the_title();?></a></li>
				<?php endforeach; ?>
			</ul>
			<h3>Recent Comments</h3>
			<?php
				global $wpdb;
				$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
				comment_post_ID, comment_author, comment_date_gmt, comment_approved,
				comment_type,comment_author_url,
				SUBSTRING(comment_content,1,28) AS com_excerpt
				FROM $wpdb->comments
				LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
				$wpdb->posts.ID)
				WHERE comment_approved = '1' AND comment_type = '' AND
				post_password = ''
				ORDER BY comment_date_gmt DESC
				LIMIT 10";
				$comments = $wpdb->get_results($sql);
				$output = $pre_HTML;
				$output .= "\n<ul>";
				foreach ($comments as $comment) {
				$output .= "\n<li>".strip_tags($comment->comment_author)
				.":" . " <a href=\"" . get_permalink($comment->ID) .
				"#comment-" . $comment->comment_ID . "\" title=\"on " .
				$comment->post_title . "\">" . strip_tags($comment->com_excerpt)
				."</a></li>";
				}
				$output .= "\n</ul>";
				$output .= $post_HTML;
				echo $output;
			?>
			<h3>Search</h3>
			<form id="searchform" method="get" action="<?php bloginfo('siteurl')?>/">
				<input type="text" name="s" id="searchbox" class="textbox" value="<?php echo wp_specialchars($s, 1); ?>" />
				<input id="btnSearch" type="submit" name="submit" value="<?php _e('Search'); ?>" />
			</form>
			<h3>Archives</h3>
 	  	<ul>
				<?php wp_get_archives('type=monthly&show_post_count=true'); ?>
			</ul>
			<h3>Meta</h3>
			<ul>
				<?php wp_register(); ?>
				<li><?php wp_loginout(); ?></li>
				<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
				<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
				<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
				<?php wp_meta(); ?>
			</ul>
			<?php endif; ?>
