<?php
/*
Plugin Name: Welcome Message
Description: Displays a welcome message to first-time visitors
Author: Benjamin J. Balter
Version: 1.0
Author URI: http://ben.balter.com
*/
	
class WP_Welcome_Message {

	public $cookie = 'hide_welcome_message';
	public $div = 'welcome_message';
	public $prepend = '#content';
	public $message = '';
	public $expiration = 365;

	/**
	 * Add hooks and load options
	 */
	function __construct() {
	
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'i18n' ) );
		add_action( 'wp_head', array( &$this, 'css' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_menu', array( &$this, 'add_settings_page' ) );
		
		//can't call this outside of function...
		$this->domain = $_SERVER['HTTP_HOST'];
		
		//load options as public vars, if set, otherwise default to the above
		$options = get_option( 'welcome_message' );
		foreach ( (array) $this as $key => $value )
			if ( isset( $options[ $key ] ) )
				$this->$key = $options[ $key ];
		
	}
	
	/**
	 * Registers JS with scripts API
	 */
	function enqueue_scripts() {
		
		$suffix = ( WP_DEBUG ) ? '.dev' : '';
		
		wp_register_script( 'jquery.cookie', plugins_url( '/js/jquery.cookie' . $suffix . '.js', __FILE__ ), array( 'jquery' ), filemtime( dirname( __FILE__ ) . '/js/jquery.cookie' . $suffix . '.js' ), true  );
		
		wp_enqueue_script( 'welcome-message', plugins_url( '/js/welcome-message' . $suffix . '.js', __FILE__ ), array( 'jquery', 'jquery.cookie' ), filemtime( dirname( __FILE__ ) . '/js/welcome-message' . $suffix . '.js' ), true );
		
	}
	
	/**
	 * Injects script vars into head
	 */
	function i18n() {
		
		wp_localize_script( 'welcome-message', 'welcome_message',  (array) $this );
		
	}
	
	/**
	 * Injects necessary CSS directly into page head to save HTTP call
	 */
	function css() { ?>
	<style>
	#welcome_message { display: none; border: 2px solid #21759B; background: #EDF9FF; text-align: center; padding: 10px; margin-bottom: 20px; }
	</style>
	<?php }
	
	/**
	 * Registers settings page with menu API
	 */
	function add_settings_page() {

		add_submenu_page( 'options-general.php', 'Welcome Message', 'Welcome Message', 'manage_options', 'welcome_message', array( &$this, 'settings_cb' ) );

	}
	
	/**
	 * Registers settings with settings API
	 */
	function register_settings() {
		register_setting( 'welcome_message', 'welcome_message', array( &$this, 'sanitize_options' ) );
	}
	
	/**
	 * Callback to display settings page
	 */
	function settings_cb() { 
		?>
		<div class="wrap">
		<h2>Welcome Message Settings</h2>
			<form action="options.php" method="post" id="welcome_message_form">
		<?php

		settings_errors();
		settings_fields( 'welcome_message' );
		$options = (array) $this;

		?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						Prepend to
					</th>
					<td>
						<input type="text" name="welcome_message[prepend]" size="20" value="<?php echo $options['prepend']; ?>" /><br />
						<span class="description">jQuery selector of element to prepend welcome message to</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						Show every
					</th>
					<td>
						<input type="text" name="welcome_message[expiration]" size="2" value="<?php echo $options['expiration']; ?>" /><br />
						<span class="description">Time in days to hide welcome message once seen</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						Domain
					</th>
					<td>
						<input type="text" name="welcome_message[domain]" size="25" value="<?php echo $options['domain']; ?>" /><br />
						<span class="description">Domain to restrict cookie to, default is usually fine, unless you want the welcome message to span multiple subdomains, e.g.</span>
					</td>
				</tr>				
				<tr valign="top">
					<th scope="row">
						Message
					</th>
					<td>
						<?php wp_editor( $options['message'], 'message', array( 'textarea_name' => "welcome_message[message]" ) ); ?>
					</td>
				</tr>
			</table>
			<input type="submit" name="submit" value="Save Changes" class="button-primary" />
			</form>
		</div>
		<?php
	}
	
	/**
	 * Sanitizes options on save
	 */
	function sanitize_options( $options ) {
		
		$options['expiration'] = (int) $options['expiration'];
		$options['message'] = wp_kses_post( $options['message'] );
		
		return $options;
		
	}
	
}

new WP_Welcome_Message();