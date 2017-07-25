<?php
/**
 * Plugin Name: RCP + Holler Box
 * Description: Custom plugin to show/hide Holler Box by status in RCP.
 * Author: 79mplus
 * Author URI: https://www.79mplus.com
 * Version: 1.2
 * Text Domain: rcp-hollerbox
 * Domain Path: /languages
 */

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if( ! class_exists( 'Mplus_HollerBox' ) ) :

class Mplus_HollerBox {

  public static $_instance;
  public $plugin_name;

  public function __construct() {
    self::defines();
		self::includes();
		self::hooks();
  }

	/**
	 * Define constants
	 */

  public function defines(){
    define( 'MP_Holler', __FILE__ );
    $this->plugin_name  = 'rcp-hollerbox';
  }

	/**
	 * Includes files
	 */

	public function includes(){
		require_once dirname( MP_Holler ) . '/includes/helper-functions.php';
	}


	/**
	 * Hooks
	 */

  public function hooks(){
		add_action( 'rcp_metabox_additional_options_before', array( $this, 'admin_restriction_box' ) );
		add_action( 'save_post', array( $this, 'save_admin_restriction_box' ) );
		add_filter( 'hwp_display_notification', array( $this, 'mplus_rcp_admin_check' ), 5, 3 );
  }

	/**
	 * Checking with RCP
	 */


	public function mplus_rcp_admin_check($show_it, $box_id, $post_id){
		if(rcp_user_can_access( get_current_user_id( ), $box_id ) && admin_check($box_id)){
			return $show_it;
		}else{
			return false;
		}
	}

	/**
	 * Adding admin restriction checkbox
	 */


	public function admin_restriction_box(){
		global $post;
		$hide_in_admin = get_post_meta(get_the_ID(), 'rcp_hide_from_admin', true);
	?>
			<p>
				<label for="rcp-hide-in-admin">
					<input type="checkbox" name="rcp_hide_from_admin" id="rcp-hide-in-admin" value="1"<?php checked( true, $hide_in_admin ); ?>/>
					<?php _e( 'Hide this content from Admin.', 'rcp-hollerbox' ); ?>
				</label>
			</p>

		 <?php
	}

	/**
	 * Saving admin restriction checkbox
	 */

	public function save_admin_restriction_box($post_id){

		$hide_in_admin = isset( $_POST['rcp_hide_from_admin'] );

		if ( $hide_in_admin ) {
			update_post_meta( $post_id, 'rcp_hide_from_admin', $hide_in_admin );
		} else {
			delete_post_meta( $post_id, 'rcp_hide_from_admin' );
		}

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

Mplus_HollerBox::instance();
