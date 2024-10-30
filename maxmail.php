<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that also follow
 * WordPress coding standards and PHP best practices.
 *
 * @package   Maxmail for Wordpress
 * @author    Igor Nadj <igor.n@optimizerhq.com>
 * @license   GPL-2.0+
 * @link      http://maxmailhq.com
 * @copyright 2013 Optimizer
 *
 * @wordpress-plugin
 * Plugin Name: Maxmail for Wordpress
 * Plugin URI:  http://maxmailhq.com
 * Description: Maxmail Subscription Form
 * Version:     1.1.0
 * Author:      Maxmail
 * Author URI:  http://maxmailhq.com
 * Text Domain: maxmailhq
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-maxmail.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'Maxmail', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Maxmail', 'deactivate' ) );

Maxmail::get_instance();