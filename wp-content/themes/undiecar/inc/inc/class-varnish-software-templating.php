<?php

/**
 * Used for modifying templates/HTML.
 *
 * @copyright Copyright (c), Varnish Software
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Varnish Software
 * @since Varnish Software 1.0
 */
class Varnish_Software_Templating {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'template_redirect',  array( $this, 'is_beaver' ) );
		add_action( 'init',               array( $this, 'slug_widget_order_class' ) );
		add_action( 'wootickets_tickets_after_quantity_input', array( $this, 'display_currency_converter' ) );

		add_filter( 'wp_nav_menu_args',   array( $this, 'modify_nav_menu_args' ) );
		add_filter( 'wp_nav_menu',        array( $this, 'indent_menu_code' ) );

	}

	/**
	 * Determining if on Beaver Builder page.
	 * Needs to be set here, due to tribe_events post-type breaking WordPress queries.
	 */
	public function is_beaver() {

		if ( is_search() ) {
			define( 'IS_BEAVER', false );
		} else if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_enabled() ) {
			define( 'IS_BEAVER', true );
		} else {
			define( 'IS_BEAVER', false );
		}

	}

	/**
	* Adds order class to widgets
	* Useful for targetting individual widgets
	*
	* Works by modifying the global array containing the sidebar class names
	* Code adapted from http://konstruktors.com/blog/wordpress/3615-add-widget-order-css-class-sidebar/
	*
	* @since 1.0
	* @global  array  $wp_registered_sidebars  List of registered widget areas
	* @global  array  $wp_registered_widgets   List of registered widgets
	* @author Ryan Hellyer <ryanhellyer@gmail.com> and Kaspars Dambis <kaspars@metronet.no> and
	*/
	public function slug_widget_order_class() {
		global $wp_registered_sidebars, $wp_registered_widgets;

		// Grab the widgets
		$sidebars = wp_get_sidebars_widgets();

		if ( empty( $sidebars ) ) {
			return;
		}

		// Loop through each widget and change the class names
		foreach ( $sidebars as $sidebar_id => $widgets ) {
			if ( empty( $widgets ) ) {
				continue;
			}
			$number_of_widgets = count( $widgets );
			foreach ( $widgets as $i => $widget_id ) {
				$wp_registered_widgets[$widget_id]['classname'] = ' col-' . ( $i + 1 );
			}
		}

	}

	/**
	 * Original Drupal theme used class of.menu which is also used by WordPress menus as widget.
	 *
	 * @param  array  $args  The menu arguments
	 * @return array  The modified menu arguments
	 */
	public function modify_nav_menu_args( $args ) {

		if ( isset( $args['menu_class'] ) && 'menu' == $args['menu_class'] ) {
			$args['menu_class'] = '';
		}

		return $args;
	}

	/**
	 * Indent the menu code.
	 * Add a .is-expandable class to each LI (to allow the original Drupal CSS to work)
	 *
	 * @param  string  $html  The menu code
	 * @return string  The modified/indented menu code
	 */
	public function indent_menu_code( $html ) {
		$html = str_replace( 'menu-item-has-children ', 'menu-item-has-children is-expandable ', $html );
		$html = varnish_indent_html( $html, "\t", "\t\t\t\t\t\t" );
		return $html;
	}

	/**
	 * Displays currency switcher on /tickets/ site.
	 */
	public function display_currency_converter() {
		if ( ! is_front_page() ) {
			echo do_shortcode("[woocs txt_type='code' show_flags='false' width='100%']");
		}
	}

}
