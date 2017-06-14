<?php
/**
 * Plugin Name: Mplus Custom Hollerbox
 * Description: Custom plugin to customize hollerbox by 79mplus
 * Author: 79mplus
 * Author URI: https://www.79mplus.com
 * Version: 1
 * Text Domain: mplus-holler
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
  public static $active = array();

  public function __construct() {
    self::defines();
		self::hooks();
  }

  public function defines(){
    define( 'MP_Holler', __FILE__ );
    $this->plugin_name  = 'mplus-custom-dokan';
  }


  public function hooks(){
    add_action('wp', array( $this, 'mplus_get_active_items' ) );
    add_action( 'wp_footer', array( $this, 'mplus_remove_actions' ), 0 );
    add_action( 'wp_footer', array( $this, 'mplus_maybe_display_items' ) );
  }

  public function mplus_remove_actions(){
    $holler = Holler_Functions::instance();
    remove_action( 'wp_footer', array( $holler, 'maybe_display_items' ) );
  }


  public function mplus_get_active_items() {

    $args = array( 'post_type' => 'hollerbox' );
    // The Query
    $the_query = new WP_Query( $args );

    // The Loop
    if ( $the_query->have_posts() ) {

      while ( $the_query->have_posts() ) {
        $the_query->the_post();
        $id = get_the_id();
        if( get_post_meta( $id, 'hwp_active', 1 ) != '1' )
        continue;

        self::$active[] = strval( $id );

      }

      /* Restore original Post Data */
      wp_reset_postdata();
    }
  }


  public function mplus_maybe_display_items() {

    // do checks for page conditionals, logged in, etc here

    foreach (self::$active as $key => $value) {

      $should_expire = get_post_meta( $value, 'expiration', 1 );
      $expiration = get_post_meta( $value, 'hwp_until_date', 1 );

      if( $should_expire === '1' && !empty( $expiration ) ) {
        // check if we've passed expiration date
        if( strtotime('now') >= strtotime( $expiration ) ) {
          delete_post_meta( $value, 'hwp_active' );
          continue;
        }
      }

      $logged_in = is_user_logged_in();
      $logged_in_meta = get_post_meta( $value, 'logged_in', 1 );

      // check logged in conditional
      if( $logged_in && $logged_in_meta === 'logged_out' || !$logged_in && $logged_in_meta === 'logged_in' )
      continue;

      $show_on = get_post_meta( $value, 'show_on', 1 );
      $page_id = get_the_ID();

      // if page conditionals set, only show on those pages
      if( is_array( $show_on ) && !in_array( $page_id, $show_on ) )
      continue;
      if(rcp_user_can_access( get_current_user_id( ), $value )){
        $this->mplus_display_notification_box( $value );
      }

    }

  }



  public function mplus_display_notification_box( $id ) {

    $avatar_email = get_post_meta($id, 'avatar_email', 1);
    ?>
    <style type="text/css">
    #hwp-<?php echo $id; ?>, #hwp-<?php echo $id; ?> a, #hwp-<?php echo $id; ?> i { color: <?php echo get_post_meta( $id, 'text_color', 1 ); ?>; }
    </style>

    <?php if( get_post_meta( $id, 'position', 1 ) != 'holler-banner' ) : ?>
      <div id="hwp-floating-btn" data-id="<?php echo $id; ?>" class="<?php echo get_post_meta( $id, 'position', 1 ); ?>"><i class="icon icon-chat"></i></div>
    <?php endif; ?>

    <div id="hwp-<?php echo $id; ?>" class="holler-box hwp-hide <?php echo get_post_meta( $id, 'position', 1 ); ?>">

      <div class="holler-inside">

        <div class="hwp-close"><i class="icon icon-cancel"></i></div>

        <?php do_action('hollerbox_above_content', $id); ?>

        <div class="hwp-box-rows">
          <?php if( !empty($avatar_email) ) echo get_avatar( apply_filters( 'hwp_avatar_email', $avatar_email, $id), 50 ); ?>
          <div class="hwp-row hwp-first-row"></div>
        </div>

        <div class="hwp-row hwp-note-optin hwp-email-row hwp-hide">
          <?php do_action('hwp_email_form', $id); ?>
        </div>

        <div class="hwp-chat hwp-hide">

          <div class="hwp-row hwp-text">
            <input type="text" class="hwp-text-input" placeholder="Type your message" />
            <i class="icon icon-mail"></i>
          </div>
        </div>

        <?php do_action('hollerbox_below_content', $id); ?>

        <?php

        $powered_by = get_option( 'hwp_powered_by' );

        if( empty( $powered_by ) ) : ?>
        <span class="hwp-powered-by"><a href="http://hollerwp.com" target="_blank">Holler Box</a></span>
      <?php endif; ?>

    </div>

  </div>
  <?php
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
