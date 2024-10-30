<?php
/**
 * Plugin Name.
 *
 * @package   Maxmail for Wordpress
 * @author    Igor Nadj <igor.n@optimizerhq.com>
 * @license   GPL-2.0+
 * @link      http://maxmailhq.com
 * @copyright 2013 Optimizer
 */

/**
 * Plugin class.
 */
class Maxmail {
	
	// TODO: update the api to return this instead of having this hardcoded
	protected static $defaultFieldSpec = array(
		'mc_fname' => array(
				'label' => 'First Name',
				'type' => 'Text Field',
				'api_key' => 'fname',
		),
		'mc_lname' => array(
				'label' => 'Last Name',
				'type' => 'Text Field',
				'api_key' => 'lname',
		),
		'mc_email' => array(
				'label' => 'Email',
				'type' => 'Text Field',
				'mandatory' => 'yes',
				'api_key' => 'email',
		),
		'mc_mob' => array(
				'label' => 'Mobile',
				'type' => 'Text Field',
				'api_key' => 'mob',
		),
		'mc_comp' => array(
				'label' => 'Company',
				'type' => 'Text Field',
				'api_key' => 'comp',
		),
		'mc_age' => array(
				'label' => 'Age Group',
				'type' => 'Text Field',
				'api_key' => 'age',
		),
		'mc_gender' => array(
				'label' => 'Gender',
				'type' => 'Dropdown List',
				'values' => array(
						'male' => 'Male',
						'female' => 'Female',
				),
				'api_key' => 'gender',
		),
		'mc_city' => array(
				'label' => 'City',
				'type' => 'Text Field',
				'api_key' => 'city',
		),
		'mc_ctry' => array(
				'label' => 'Country',
				'type' => 'Text Field',
				'api_key' => 'country',
		),

	);
	

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'maxmail';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Add Settings link to Plugin Page
		add_filter('plugin_action_links', array($this, 'maxmail_action_links'), 10, 2 );
		
		// Register ShortCode
		add_shortcode('maxmail', array($this, 'display_public_subscription_form'));
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array( ), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( ), $this->version );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Change 'Page Title' to the title of your plugin admin page
		 * Change 'Menu Text' to the text for menu item for the plugin settings page
		 * Change 'plugin-name' to the name of your plugin
		 */
		$this->plugin_screen_hook_suffix = add_plugins_page(
			__( 'Maxmail', $this->plugin_slug ),
			__( 'Maxmail', $this->plugin_slug ),
			'read',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	public function maxmail_action_links($links, $pluginLink){
		if(strpos($pluginLink, 'maxmail') === false) return $links;
		return array_merge(
			array(
					'<a href="'.admin_url('plugins.php?page=maxmail').'">'.__('Settings', $this->plugin_slug).'</a>'
			),
			$links
		);
	}
	
	

	
	
	/*
	 * Views
	*/
	
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}
	
	public function display_public_subscription_form(){
		include_once( 'views/public.php' );
	}
	
	
	
	/*
	 * Public Methods
	 */
	
	public function getAccountEmail(){
		return get_option('maxmail_account_email');
	}
	
	public function setAccountEmail($email){
		update_option('maxmail_account_email', $email);
	}
	
	public function getApiKey(){
		return get_option('maxmail_api_key');
	}
	
	public function setApiKey($apiKey){
		update_option('maxmail_api_key', $apiKey);
	}
	
	public function isConfigured(){
		return $this->hasCredentials(); 
	}
	
	public function hasCredentials(){
		return $this->getAccountEmail() && $this->getApiKey();
	}
	
	public function isMisconfigured(){
		if(!$this->hasCredentials()){
			return false; // cannot be mis-configured if we have no config
		}
		return $this->apiGetMailingLists() === false;
	}
	
	public function hasList(){
		return $this->getListId() !== null;
	}
	
	
	/**
	 * @return array of id => name
	 */
	public function getLists(){
		if($this->isConfigured() && !$this->isMisconfigured()){
			$data = $this->apiGetMailingLists();
			$lists = isset($data['MailingLists']) ? $data['MailingLists'] : array();
			
			$r = array();
			foreach($lists as $id => $info){
				$r[$id] = $info['name'];
			}
			return $r;
		}else{
			return array();
		}
	}
	
	/**
	 * @return null|number
	 */
	public function getListId(){
		$r = get_option('maxmail_list_id');
		if(trim($r) === '' || $r === null) return null;
		return $r;
	}
	
	public function getListName(){
		$lists = $this->getLists();
		$id = $this->getListId();
		if(isset($lists[$id])){
			return $lists[$id];
		}
		return '';
	}
	
	public function setList($id){
		update_option('maxmail_list_id', $id);
	}
	
	public function getFields($id){
		if($this->isConfigured() && !$this->isMisconfigured()){
			
			$r = array();
			$data = $this->apiGetMailingLists();
			$fields = isset($data['MailingLists'][$id]['fields']) ? $data['MailingLists'][$id]['fields'] : array();
			$defaultFields = self::$defaultFieldSpec;
			$returnFields = array_merge($fields, $defaultFields);
				
			// strip all inactive fields
			$r = array();
			foreach($returnFields as $fieldName => $spec){
				if(isset($spec['type']) && $spec['type'] !== null){
					$r[$fieldName] = $spec;
				}
			}
			return $r;
			
		}else{
			return array();
		}
	}
	
	public function setIsVisible($fieldName, $isVisible){
		$visibleFields = $this->getVisibleFields();
		$visibleFields[$fieldName] = $isVisible;
		update_option('maxmail_visible_fields', $visibleFields);
	}
	
	public function isVisible($fieldName){
		$visibleFields = $this->getVisibleFields();
		if(isset($visibleFields[$fieldName])) return $visibleFields[$fieldName];
		return true; // default to visible
	}
	
	protected function getVisibleFields(){
		$r = get_option('maxmail_visible_fields');
		if(!$r) return array();
		return $r;
	}
	
	/**
	 * @return boolean true or false
	 * @param array $data to pass in to the maxmail subscribe api
	 */
	public function subscribe(array $data){
		if($this->isConfigured() && !$this->isMisconfigured()){
			$data['mailing_list'] = $this->getListId();
			$r = $this->api('subscription.php', $data);
			return isset($r['success']) && $r['success'] == 'true';
			
		}else{
			return false;
		}
	}
	
	
	
	
	
	/*
	 * Internal Functions
	 */

	
	protected function api($api, array $data = array()){
		$data['user_email'] = $this->getAccountEmail();
		$data['api_key'] = $this->getApiKey();
		$data['format'] = 'json';
		$url = 'https://api.maxmailhq.com/'.$api . '?' . http_build_query($data);
		$html = $this->curl($url, $data);
		return json_decode($html, true);
	}
	
	protected function curl($url, $data){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$r = curl_exec($ch);
		curl_close($ch);
		return $r;
	}
	

	
	/**
	 * @return array|false
	 */
	protected function apiGetMailingLists(){
		if(!isset($this->mailingListsCache)){
			$data = $this->api('getMailingLists.php');
			if(isset($data['success']) && $data['success'] == 'true'){
				$this->mailingListsCache = $data;
			}else{
				return false;
			}
		}
		return $this->mailingListsCache;
	}
	
}