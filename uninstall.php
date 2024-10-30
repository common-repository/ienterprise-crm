<?php
if (! defined ( 'ABSPATH' )) {
	exit (); // Exit if accessed directly
}

// If uninstall is not called from WordPress, exit
if (! defined ( 'WP_UNINSTALL_PLUGIN' )) {
	exit ();
}

delete_option ( 'ienterprisecrm_endpoint' );
delete_option ( 'ienterprisecrm_username' );
delete_option ( 'ienterprisecrm_password' );
delete_option ( 'ienterprisecrm_is_connected' );
delete_option ( 'ienterprisecrm_recaptcha' );
delete_option ( 'ienterprisecrm_submitbtn' );
delete_option ( 'ienterprisecrm_fields' );
delete_option ( 'ienterprisecrm_keywords' );