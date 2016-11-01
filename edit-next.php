<?php
/*
Plugin Name: Edit Next Post
Description: Switch to another post in edit screen
Plugin URI: https://wordpress.org/plugins/edit-next
Author: Nazmul Ahsan
Author URI: http://nazmulashan.me
Version: 1.0.0
License: GPL2
Text Domain: edit-next
Domain Path: /languages
*/

/*

    Copyright (C) 2016  Nazmul Ahsan  n.mukto@gmail.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class for the plugin
 * @package WordPress
 * @subpackage CB_Edit_Next
 * @author Nazmul Ahsan
 */
if( ! class_exists( 'CB_Edit_Next' ) ) :

class CB_Edit_Next {
	
	public static $_instance;
	public $plugin_name;
	public $plugin_version;

	public function __construct() {
		self::define();
		self::hooks();
	}

	/**
	 * Define something
	 */
	public function define(){
		define( 'CB_EDIT_NEXT', __FILE__ );
		$this->plugin_name = 'edit-next';
		$this->plugin_version = '1.0.0';
	}

	/**
	 * Hooks
	 */
	public function hooks(){
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10, 2 );
	}

	/**
	 * Enqueue JavaScripts and stylesheets
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( $this->plugin_name . '-select2', plugins_url( '/assets/css/select2.min.css', CB_EDIT_NEXT ), '', '4.0.3', 'all' );
		wp_enqueue_script( $this->plugin_name . '-select2', plugins_url( '/assets/js/select2.min.js', CB_EDIT_NEXT ), array('jquery'), '4.0.3', true );
		wp_enqueue_script( $this->plugin_name, plugins_url( '/assets/js/script.js', CB_EDIT_NEXT ), array('jquery'), $this->plugin_version, true );
	}

	/**
	 * Add JS variables to admin_head
	 */
	public function admin_head() {
		echo '<script>var edit_post_url = "' . admin_url( 'post.php' ) . '";</script>';
	}

	/**
	 * Add a new meta box in post edit screen
	 */
	public function add_meta_box( $post_type, $post ) {
		add_meta_box( $this->plugin_name, __( 'Edit Next', $this->plugin_name ), array( $this, 'meta_box' ), $post_type, 'side', 'high' );
	}

	/**
	 * Meta box callback
	 */
	public function meta_box() {
		$post_type = get_current_screen()->post_type;

		$args = array(
			'post_type'			=> $post_type,
			'posts_per_page'	=> -1
			);

		wp_reset_query();
		$p = new WP_Query( $args );

		$html = '<span>Choose another ' . $post_type . ' to edit</span>';
		$html .= '<select id="select-edit-next" style="width:100%">';
		$html .= '<option value=""> - Choose another ' . $post_type . ' - </option>';

		// we are using foreach instead of have_posts() to avoid post slug conflict issues
		foreach( $p->posts as $post ) :
			if( current_user_can( 'edit_post', $post->ID ) ) :
			$html .= '<option value="' . $post->ID . '" ' . selected( $_GET['post'], $post->ID, false ) . '>' . $post->post_title . ' (' . ucwords( $post->post_status ) . ')' . '</option>';
			endif;
		endforeach;
		wp_reset_query();

		$html .= '</select>';

		echo $html;
	}

	/**
	 * Cloning is forbidden.
	 */
	private function __clone() { }

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	private function __wakeup() { }

	/**
	 * Instantiate the plugin
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

endif;

CB_Edit_Next::instance();