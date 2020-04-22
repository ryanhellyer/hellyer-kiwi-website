<?php

/**
 * Strattic Posts.
 * 
 */
class Strattic_Posts extends Strattic_Core {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'future_post', array( $this, 'publish_post' ), 10, 2);
		add_action( 'publish_post', array( $this, 'publish_post' ), 10, 2 );
		
		add_action( 'current_screen',  array( $this, 'handle_current_screen') );
		add_action( 'enqueue_block_editor_assets',  array( $this, 'myguten_enqueue') );

	}

	public function publish_post($ID, $post ) {
		$ts = strtotime($post->post_date_gmt);
		$status = 'scheduled';
		if ($post->post_status == 'publish') {
			$status = 'cancelled';
		}
		$postData = array(
			'scheduleId' => $ID,
			'ts' => $ts,
			'status' => $status,
			// 'post' => $post
		);
		$requestBody = json_encode($postData);

		$site_id = $this->get_current_site_strattic_id();
		$path = 'sites/' . $site_id . '/schedule';
		// print_r($path);
		// die;
		$response = $this->make_api_request( $path, 'POST', $postData );
	}

	function handle_current_screen() {
		global $pagenow;

		if (( $pagenow == 'post.php' ) || $pagenow == 'post-new.php' || (get_post_type() == 'post')) {
			add_action( 'admin_notices',  array( $this, 'strattic_posts_notice') );
		}

	}

	function myguten_enqueue() {

		wp_enqueue_script(
			'strattic-post',
			STRATTIC_ASSETS . 'blockeditor.js',
			array(),
			STRATTIC_VERSION
		);
	}

	function strattic_posts_notice() {
		?>
		<div class="notice notice-warning">
			<p>
				New Strattic feature: scheduled posts! 
				<br/>
				Please note that scheduled posts publish your whole site at the designated time so please Save as Draft if you don’t want your post or page to be public.
			</p>
		</div><?php
	}

}