<?php
/*
* Dashboard Custom Post type
*/
if ( ! class_exists( 'PP_Dashboard_Post_Type' ) )
{
	class PP_Dashboard_Post_Type
	{
		function __construct()
		{
			/**
			* Create PP Dashboard CPT
			*
			* Function Name: pp_dashboard_init
			*
			* @access public
			*
			**/
			add_action( 'init', array( &$this, 'pp_dashboard_init' ) );

			/**
			* Add Meta Box to PP_Dashboard CPT
			*
			* Function Name: pp_dashboard_meta_box_add
			*
			* @access public
			*
			**/
            add_action( 'add_meta_boxes', array( &$this, 'pp_dashboard_meta_box_add' ) );

            /**
			* Save data from meta box
			*
			* Function Name: pp_dashboard_metabox_save
			*
			* @access public
			* @param array  $pp_dashboard_box : Holds parameters to be passed to add_meta_box function
			* @param int    $post_id : Contains ID of post.
			* @param int 	$old : Old values of inputs saved in database
			* @param int 	$new : New values of inputs to be updated in database
			*
			*/
            add_action( 'save_post', array( &$this, 'pp_dashboard_metabox_save' ) );

            /**
			* Updating messages
			*
			* Function Name: pp_dashboard_set_messages
			*
			* @access public
			* @param var  $message : Holds message to be displayed
			*
			**/
            add_filter( 'post_updated_messages', array( &$this, 'pp_dashboard_set_messages' ) );

            /**
			* Create company name & client location field in PP_Dashboard post
			*
			* Function Name: add_thumbnail_column
			*
			* @access public
			*
			**/
            add_filter( 'manage_edit-pp_dashboards_columns', array( &$this, 'add_thumbnail_column'), 10, 1 );
			add_action( 'manage_pp_dashboards_posts_custom_column', array( &$this, 'display_thumbnail' ), 10, 1 );
			add_filter( 'enter_title_here',array( &$this, 'pp_dashboard_default_title' ));
            //add_filter( 'wp_title' , array(&$this, 'pp_dashboard_archive_titles'),900);
            add_filter('wp_title', array(&$this, 'pp_dashboard_archive_title'), 10, 2);

			add_filter( 'pre_get_posts' , array(&$this, 'hide_pp_dashboard_template'),900);            
			// add_filter('', array(&$this, ''));

            //add_filter( 'manage_posts_columns', array(&$this, 'pp_dashboard_columns_head'));

            

            /**
			* Rename Featured image title text
			*
			* Function Name: client_pp_dashboard_change_image_box
			*
			* @access public
			*
			**/
			add_action( 'do_meta_boxes', array( &$this,'client_pp_dashboard_change_image_box' ) );

            global $pp_dashboard_box;
			$prefix = 'pp_dashboard_';
			$pp_dashboard_box = array(
			    'id' => 'pp_dashboardid',
			    'title' => 'PP_Dashboard Information',
				'page' => 'Dashboard',
			    'context' => 'pp_dashboard',
			    'priority' => 'high',
			    'fields' => array(
			    	array(
			            'name' => 'Sort Position',
			            'desc' => 'PP_Dashboard Listing Page position',
			            'id' => $prefix . 'sort_order',
			            'type' => 'number',
			            'std' => '1'
			        ),					
				)
			);
		}

		/**
		* Create PP_Dashboard CPT
		*
		* Function Name: pp_dashboard_init
		*
		* @access public
		*
		**/
		function pp_dashboard_init()
		{

			$labels_pp_dashboard = array(
								'name' 					=> __('Dashboards','dts'),
								'singular_name' 		=> __('Dashboard','dts'),
								'add_new' 				=> __('Add Dashboard Item','dts'),
								'add_new_item' 			=> __('Add New Dashboard Item','dts'),
								'edit_item' 			=> __('Edit Dashboard Item','dts'),
								'new_item' 				=> __('New Dashboard Item','dts'),
								'all_items' 			=> __('All Dashboard Items','dts'),
								'search_items' 			=> __('Search Dashboard Items','dts'),
								'not_found' 			=> __('No Dashboard Items found','dts'),
								'not_found_in_trash' 	=> __('No Dashboard Items found in Trash','dts'),
								'parent_item_colon'		=> '',
								'menu_name' 			=> __('PP Dashboards','dts')
							);
			$argspp_dashboard = array(
							'labels' 			=> $labels_pp_dashboard,
							'public' 			=> true,
							'show_ui' 			=> true,
							'rewrite' 			=> array( 'slug' => 'pp_dashboards' ),
							'capability_type' 	=> 'mange_options',
							'menu_icon' 		=> 'dashicons-id',
							'hierarchical' 		=> true,
							'supports' 			=> array( 'title', 'editor', 'thumbnail' ),
							'exclude_from_search' => false,
							'publicly_queryable' => true,
							'show_in_menu' => true,
							'show_in_nav_menus' =>false,
							'show_in_admin_bar' => false,
							'has_archive' => true,
							'query_var'             => true,
						);

        	 register_post_type( 'pp_dashboards', $argspp_dashboard );

			 /*
			 // Add new taxonomy, NOT hierarchical (like tags)
			$labelscat_pp_dashboard = array(
				'name'                       => __( 'PP_Dashboard Category', 'dts' ),
				'singular_name'              => __( 'PP_Dashboard Category',  'dts' ),
				'search_items'               => __( 'Search PP_Dashboard Category', 'dts' ),
				'popular_items'              => __( 'Popular PP_Dashboard Category', 'dts' ),
				'all_items'                  => __( 'All PP_Dashboard Category', 'dts' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => __( 'Edit PP_Dashboard Category', 'dts' ),
				'update_item'                => __( 'Update PP_Dashboard Category', 'dts' ),
				'add_new_item'               => __( 'Add New PP_Dashboard Category', 'dts' ),
				'new_item_name'              => __( 'New PP_Dashboard Category Name', 'dts' ),
				'separate_items_with_commas' => __( 'Separate PP_Dashboard Category with commas', 'dts' ),
				'add_or_remove_items'        => __( 'Add or remove PP_Dashboard Category', 'dts' ),
				'choose_from_most_used'      => __( 'Choose from the most used PP_Dashboard Category', 'dts' ),
				'not_found'                  => __( 'No PP_Dashboard Category found.', 'dts' ),
				'menu_name'                  => __( 'PP_Dashboard Category', 'dts' ),
			);

			 $args_cat_pp_dashboard = array(
				'hierarchical'          => true,
				'labels'                => $labelscat_pp_dashboard,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'public'          => true,
				'publicly_queryable'          => true,
				'show_in_nav_menus'          => true,
				'show_in_quick_edit'          => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array( 'slug' => 'b3m_pp_dashboard_category' ),
			);
			*/

			// register_taxonomy( 'b3m_pp_dashboard_category', 'pp_dashboards', $args_cat_pp_dashboard );
			// register_taxonomy_for_object_type( 'b3m_pp_dashboard_category', 'pp_dashboards' );
        }

        /**
		* Add Meta Box to PP_Dashboard CPT
		*
		* Function Name: pp_dashboard_meta_box_add
		*
		* @access public
		*
		**/
        function pp_dashboard_meta_box_add()
		{
			global $pp_dashboard_box;
			remove_meta_box('wpseo_meta', 'pp_dashboards', 'normal');
			//add_meta_box( $pp_dashboard_box['id'], $pp_dashboard_box['title'], array( &$this, 'pp_dashboard_meta_box_cb' ), 'pp_dashboards', 'normal','high' );
		}


		/**
		* Callback Function of PP_Dashboard Meta Box
		*
		* Function Name: pp_dashboard_meta_box_cb
		*
		* @access public
		*
		**/
		function pp_dashboard_meta_box_cb()
		{
    		global $pp_dashboard_box, $post;

			echo '<input type="hidden" name="pp_dashboard_meta_box_nonce" value="', wp_create_nonce( basename(__FILE__) ), '" />';
			echo '<table class="form-table">';
			foreach ( $pp_dashboard_box['fields'] as $field )
			{
		        // get current post meta data
		        $pp_dashboard_check_value = get_post_meta( $post->ID, $field['id'], true );
		        echo '<tr class="form-field">',
		                '<th scope="row"><label for="', $field['id'], '">', _e($field['name'],'aiinfo'), '</label></th>',
		                '<td>';
		        switch ( $field['type'] )
		        {
		            case 'text':
		                echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $pp_dashboard_check_value ? $pp_dashboard_check_value : $field['std'], '" />', '<br />', $field['desc'];
		                break;
		            case 'number':
		                echo '<input type="number" name="', $field['id'], '" id="', $field['id'], '" value="', $pp_dashboard_check_value ? $pp_dashboard_check_value : $field['std'], '" />', '<br />', $field['desc'];
		                break;
		            case 'twitter-text':
		                echo '<input type="text" maxlength="140" name="', $field['id'], '" id="', $field['id'], '" value="', $pp_dashboard_check_value ? $pp_dashboard_check_value : $field['std'], '" />', '<br />', $field['desc'];
		                break;
		            case 'email':
		                echo '<input type="email" name="', $field['id'], '" id="', $field['id'], '" value="', $pp_dashboard_check_value ? $pp_dashboard_check_value : $field['std'], '" />', '<br />', $field['desc'];
		                break;
		             case 'textarea':
		                echo '<textarea placeholder="', $field['desc'], '" style="height:100px;" name="', $field['id'], '" id="', $field['id'], '">', $pp_dashboard_check_value ? $pp_dashboard_check_value : $field['std'], '</textarea>';
		                break;
		             case 'checkbox':
		                echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '" '.checked($pp_dashboard_check_value,"on" ).'/>', $field['desc'];
		                break;

		        }
		        echo  '</td><td>',
		            '</td></tr>';
		    }
			echo '</table>';
		}

		/**
		* Save data from meta box
		*
		* Function Name: pp_dashboard_metabox_save
		*
		* @access public
		* @param array  $pp_dashboard_box : Holds parameters to be passed to add_meta_box function
		* @param int    $post_id : Contains ID of post.
		* @param int 	$old : Old values of inputs saved in database
		* @param int 	$new : New values of inputs to be updated in database
		*
		*/
		function pp_dashboard_metabox_save( $post_id )
		{
		    global $pp_dashboard_box;

		    // verify nonce
		    if ( isset($_POST['pp_dashboard_meta_box_nonce']) && !wp_verify_nonce( $_POST['pp_dashboard_meta_box_nonce'], basename(__FILE__) ) )
				{
		        return $post_id;
		    }

		    // check autosave
		    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		        return $post_id;
		    }

		    // check permissions
		    if ( 'page' == $_POST['post_type'] ) {
		        if (!current_user_can('edit_page', $post_id)) {
		            return $post_id;
		        }
		    } elseif (!current_user_can('edit_post', $post_id)) {
		        return $post_id;
		    }

		    foreach ( $pp_dashboard_box['fields'] as $field ) {
		        $old = get_post_meta( $post_id, $field['id'], true );
		        $new = $_POST[$field['id']];
		        if ( $new && $new != $old ) {
		            update_post_meta( $post_id, $field['id'], $new );
		        } elseif ('' == $new && $old) {
		            delete_post_meta( $post_id, $field['id'], $old );
		        }
		    }
		}

		/**
		* Create company name & client location field in PP_Dashboard post
		*
		* Function Name: add_thumbnail_column
		*
		* @access public
		*
		**/
		function add_thumbnail_column( $columns )
		{
			unset($columns['wpex_post_thumbs']);
			unset($columns['wpseo-score']);
			unset($columns['wpseo-links']);
			unset($columns['wpseo-score-readability']);

			$column_pp_dashboard = array( 'sort_order' => __('Sort Order','pp_dashboards' ));
			$columns = array_slice( $columns, 0, 1, true ) + array_slice( $columns, 1, 1, true ) + $column_pp_dashboard + array_slice( $columns, 1, NULL, true );
			return $columns;
		}

		function display_thumbnail( $column )
		{
			global $post;

			switch ( $column ) {
				case 'sort_order':
					echo  $sort_order = get_post_meta($post->ID, 'pp_dashboard_sort_order', true);
					break;

			}
		}

		function hide_pp_dashboard_template($query) {
			global $pagenow;
 
			if( 'edit.php' != $pagenow || !$query->is_admin )
    			return $query;
 
 			$user = wp_get_current_user();
 			$roles = wp_get_current_user()->roles;


			if (!in_array("administrator", $roles)) { 
				$post_status = array( 'publish', 'pending', 'draft', 'future' );
				$query->set("post_status", $post_status);	    			
    		}
			return $query;
		}

	

		/**
		* Updating messages
		*
		* Function Name: pp_dashboard_set_messages
		*
		* @access public
		* @param var  $message : Holds message to be displayed
		*
		**/
		function pp_dashboard_set_messages($messages) {

			global $post, $post_ID;
			$post_type = get_post_type( $post_ID );

			$obj = get_post_type_object( $post_type );
			$singular = $obj->labels->singular_name;

			$messages[$post_type] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __($singular.' updated. <a href="%s">View '.strtolower($singular).'</a>'), esc_url( get_permalink($post_ID) ) ),
			2 => __($singular .'Custom field updated.'),
			3 => __($singular.'Custom field deleted.'),
			4 => __($singular.' updated.'),
			5 => isset($_GET['revision']) ? sprintf( __($singular.' restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __($singular.' published. <a href="%s">View '.strtolower($singular).'</a>'), esc_url( get_permalink($post_ID) ) ),
			7 => __('Page saved.'),
			8 => sprintf( __($singular.' submitted. <a target="_blank" href="%s">Preview '.strtolower($singular).'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __($singular.' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview '.strtolower($singular).'</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __($singular.' draft updated. <a target="_blank" href="%s">Preview '.strtolower($singular).'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			);
			return $messages;
		}

		/**
		* Rename Featured image title text
		*
		* Function Name: client_pp_dashboard_change_image_box
		*
		* @access public
		*
		**/
		function client_pp_dashboard_change_image_box()
		{
			remove_meta_box( 'postimagediv', 'pp_dashboards', 'side' );
    		add_meta_box( 'postimagediv', __('Client Image (150px * 150px)'), 'post_thumbnail_meta_box', 'pp_dashboards', 'side', 'low' );
		}

		function pp_dashboard_default_title( $title ){
			$screen = get_current_screen();
			if ( 'pp_dashboards' == $screen->post_type ){
				$title = 'Enter the name';
			}
			return $title;
		}

		
		
	}
	new PP_Dashboard_Post_Type;


}
?>
