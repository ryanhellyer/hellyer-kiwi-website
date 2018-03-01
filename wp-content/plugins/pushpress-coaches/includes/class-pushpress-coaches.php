<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    pushpress_coaches
 * @subpackage pushpress_coaches/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    pushpress_coaches
 * @subpackage pushpress_coaches/includes
 */
class Pushpress_Coaches {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Pushpress_Coaches_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $pushpress_coaches    The string used to uniquely identify this plugin.
	 */
	protected $pushpress_coaches;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->pushpress_coaches = 'pushpress_coaches';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();		
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Pushpress_Coaches_Loader. Orchestrates the hooks of the plugin.
	 * - Pushpress_Coaches_i18n. Defines internationalization functionality.
	 * - Pushpress_Coaches_Admin. Defines all hooks for the admin area.
	 * - Pushpress_Coaches_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pushpress-coaches-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pushpress-coaches-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pushpress-coaches-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pushpress-coaches-public.php';

		/**
		 * The class responsible for defining all data model stuff needed in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pushpress-coaches-model.php';
		
		/**
		 * The class responsible for defining all shortcodes
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pushpress-coaches-shortcodes.php';

		/**
		 * The class responsible for defining the coach custom post type
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/post-type-pushpress-coach.php';

		$this->loader = new Pushpress_Coaches_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Pushpress_Coaches_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Pushpress_Coaches_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Pushpress_Coaches_Admin( $this->get_pushpress_coaches(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Pushpress_Coaches_Public( $this->get_pushpress_coaches(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// listen for coache submits
		add_action( 'gform_pre_submission', array($this,'before_user_form_submission') );
		add_action( 'gform_after_submission', array($this,'after_user_form_submission'), 10, 2 );

		add_filter('single_template', array($this,'custom_single_template'));
		add_filter('archive_template', array($this,'custom_archive_template'));


	}

	public function after_user_form_submission($entry, $form) { 
		if($form['cssClass'] == "pushpress-coach-bio-request") { 
			return $this->after_coach_submit($entry, $form);
		}
	}


	public function before_user_form_submission( $form ) {
		
	}
	
	public function after_coach_submit( $entry, $form ) {

		$blog_id = get_current_blog_id();
		$is_main_site = is_main_site($blog_id);		

		//echo '<pre>';
		//echo'<h1>Submitted Coach Form</h1>';
		//var_dump($entry);
		//var_dump($form['fields']);
		
		
		global $wpdb;

		$responses = array();
		$_fields = array();
		$post_title = "";
		//var_dump($entry);

		foreach ($form['fields'] as $field) { 
			//echo "<br>" . $field['type'];
			// parse the headshot
			if ($field['type'] == "post_image") { 

				$file_path = $entry[$field['id']];
				$path_to_headshot = explode("/", $file_path);
				$filename = $path_to_headshot[count($path_to_headshot)-1];

				$file_meta = explode("|", $filename);
				//var_dump($file_meta);

				$filename = $file_meta[0];
				$featured_image_post_id = $file_meta[count($file_meta)-1];
				//echo "<br>found filename for headshot: " . $filename;
				//echo "<br>Found Post ID: " . $featured_image_post_id;

				$featured_image = get_post($featured_image_post_id);
				//echo '<br>post:<br>';
				//var_dump($featured_image);


				$featured_image_link = $featured_image->guid;

				// echo "<br>Image Link: " . $featured_image_link;
				//echo '<img src="' . $featured_image_link. '"/>';				

				// only do this for dev and prod
												
			}

			if ($field['inputs']) { 
				
				foreach ($field['inputs'] as $input ) { 
					
					if (isset($entry[$input['id']]) && strlen(trim($entry[$input['id']]))) { 
						$_fields[$input['id']] = $input;
						$_fields[$input['id']]['adminLabel'] = strtolower($input['label'] . "_" . $field['label']);
						$_fields[$input['id']]['type'] = $input['type'];

						$responses[$input['id']] = array(
							"field_id" => $input['id'],
							"label" => "coach_" . strtolower($input['label'] . "_" . $field['label']),
							"value" => $entry[$input['id']]
						);

						if ($field['type'] == "name") { 
							$post_title .= " " . $entry[$input['id']];
						}
					}
					
				}
			}
			else { 
				$_fields[$field['id']] = $field;
				$responses[$field['id']] = array(
					"field_id" => $field['id'],
					"label" => $field['adminLabel'],
					"value" => $entry[$field['id']]
				);
			}
		}

		
		// parse the headshot
		//$headshot = $responses[8]

		//echo '<br>Fields<br>';
		//var_dump($_fields);
		
		//echo '<bR>Responses<br>';
		//var_dump($responses);

		$args = array(
		    'post_type'=> 'coaches',
		    'post_status'    => 'private',
		    'order'    => 'ASC'
		);           
		$posts = get_posts( $args );

		$post = $posts[0];

		
		$post_params = array(
			'post_author' => 0,
			'post_date' => current_time('mysql'),
			'post_date_gmt' => current_time('mysql'),
			'post_content'   => "",
			'post_title' => $post_title,
			'post_status' => 'draft',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_type' => 'coaches',
		);

		//echo "<pre>";
		//echo '<bR>Post Params<br>';
		//var_dump($post_params);		

		$post_meta = array();
		foreach ($responses as $resp) { 
			
			//$f = $_fields[$resp['field_id']];
			//var_dump($f->adminLabel);
			//echo "<br>Workign on field: " . $resp['field_id'] . "(" . $resp['label'] . ")";
			//var_dump($resp);

			if (strlen(trim($resp['label']))) { 
				$post_meta[$resp['label']] = nl2br($resp['value']);
			}				
		}

		
		if ($featured_image_post_id) { 
			$post_meta['_thumbnail_id'] = $featured_image_post_id;
		}

		// blank fields by default
		$post_meta['coach_sort_order'] = 1;
		$post_meta['coach_phone'] = "";
		$post_meta['coach_email'] = "";
		$post_meta['coach_facebook'] = "";
		$post_meta['coach_twitter'] = "";
		$post_meta['coach_instagram'] = "";
		$post_meta['coach_website'] = "";
		$post_meta['coach_intro'] = "";

		// default divi builder
		// $post_meta['_et_pb_use_builder'] = 'on';



		//echo '<bR>Post Meta<br>';
		// var_dump($post_meta);
		// die();


		$post_id = wp_insert_post($post_params);


		foreach ($post_meta as $key=>$value) { 
			$meta = update_post_meta($post_id, $key, $value);
			///echo '<br>inserting meta:<br>';
			///var_dump($meta);
		}

		
		if (PP_ENV !== "local") { 
		
			//echo '<pre>';
			//echo "<Br>FEATURED IMAGE LINK . " . $featured_image_link;
			$img_path = wp_upload_dir(null, true);
			//var_dump($img_path);
		
			// remove the upload path to form the local path
			$local_file_path = str_replace($img_path['baseurl'], "", $featured_image_link);
			$imgix_file_path = $blog_id . '/' . $local_file_path;

			//.echo "<br>LOCAL FILE PATH: " . $local_file_path;	
			//.echo "<br>IMGIX PATH: " . $imgix_file_path;	

			
			if (PP_ENV == "prod") { 
				$imgix_url = "https://pushpress-sites.imgix.net/" . $imgix_file_path . "?w=500&h=500&fit=facearea&facepad=4.0";
			}
			else { 
				$imgix_url = "https://pushpress-sites-dev.imgix.net/" . $imgix_file_path . "?w=500&h=500&fit=facearea&facepad=4.0";
			}

			//echo "<br>IMGIX Image URL: " . $imgix_url;

			$local_upload_path = $img_path['basedir'] . $local_file_path;
			//echo "<br>upload path: " . $local_upload_path;
			
			$content = file_get_contents($imgix_url);
			file_put_contents($local_upload_path, $content);   
			
		}

		//var_dump($form);
		
	} 

	public function custom_single_template($single) {
		echo $single;

		global $wp_query, $post;
		
	    // Checks for single template by post type 
	    if ( $post->post_type == 'coaches' ) {
	        if ( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/templates/single-coach.php' ) ) {
	            return plugin_dir_path( dirname( __FILE__ ) ) . 'includes/templates/single-coach.php';
	        }
	    }

	    return $single;

	}
	public function custom_archive_template($template) {
		
		global $wp_query, $post;
		
	    if ( $post->post_type == 'coaches' ) {
	        if ( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/templates/archive-coach.php' ) ) {
	            return plugin_dir_path( dirname( __FILE__ ) ) . 'includes/templates/archive-coach.php';
	        }	        
	    }
	    return $template;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {		
		$this->loader->run();		
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_pushpress_coaches() {
		return $this->pushpress_coaches;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Pushpress_Coaches_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}	
}
