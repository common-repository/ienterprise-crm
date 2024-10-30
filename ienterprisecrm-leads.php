<?php
/*
 * Plugin Name: iEnterprise CRM
 * Description: iEnterprise CRM WordPress plugin allows you to connect your WordPress site with iEnterprise CRM and create Leads from your site
 * Plugin URI: https://ienterprises.com/products/ienterprise-crm/?campaign_id=WordpressStore01&product=EmpowerCRM&data_source=NA&lead_source=Other&trial=1
 * Version: 1.0.8
 * Author: iEnterprises
 * Author URI: https://ienterprises.com
 */
if (! defined ( 'ABSPATH' )) {
	exit (); // Exit if accessed directly
}

error_reporting ( 5 );

define ( 'IENTERPRISECRM_DEBUG', false );
define ( 'IENTERPRISECRM_PLUGIN_DIR', plugin_dir_path ( __FILE__ ) );
define ( 'IENTERPRISECRM_PLUGIN_URL', plugin_dir_url ( __FILE__ ) );

include 'inc/settings-page.php';
include 'inc/settings-fields.php';
include 'inc/shortcode-form.php';

register_activation_hook ( __FILE__, 'ienterprisecrm_activate' );
function ienterprisecrm_activate() {
	// if (! get_option ( 'ienterprisecrm_endpoint' )) {
	update_option ( 'ienterprisecrm_endpoint', 'http://54.225.151.135' );
	// }
	if (! get_option ( 'ienterprisecrm_submitbtn' )) {
		update_option ( 'ienterprisecrm_submitbtn', 'Submit' );
	}
}
