<?php
if (! defined ( 'ABSPATH' )) {
	exit (); // Exit if accessed directly
}

add_action ( 'admin_init', 'ienterprisecrm_settings_api_init' );
function ienterprisecrm_settings_api_init() {
	$id = "ienterprisecrm_section";
	$title = "Connect to iEnterprise CRM";
	$callback = "ienterprisecrm_settings_section";
	$page = "ienterprisecrm";
	add_settings_section ( $id, $title, $callback, $page );
	
	$id = "ienterprisecrm_endpoint";
	$title = "Login";
	$callback = "ienterprisecrm_endpoint_field";
	$page = "ienterprisecrm";
	$section = 'ienterprisecrm_section';
	add_settings_field ( $id, $title, $callback, $page, $section, $args = array () );
	register_setting ( "ienterprisecrm", "ienterprisecrm_endpoint", "ienterprisecrm_endpoint_sanitize" );
	
	$id = "ienterprisecrm_username";
	$title = "Username";
	$callback = "ienterprisecrm_username_field";
	$page = "ienterprisecrm";
	$section = 'ienterprisecrm_section';
	add_settings_field ( $id, $title, $callback, $page, $section, $args = array () );
	register_setting ( "ienterprisecrm", "ienterprisecrm_username" );
	
	$id = "ienterprisecrm_password";
	$title = "Password";
	$callback = "ienterprisecrm_password_field";
	$page = "ienterprisecrm";
	$section = 'ienterprisecrm_section';
	add_settings_field ( $id, $title, $callback, $page, $section, $args = array () );
	register_setting ( "ienterprisecrm", "ienterprisecrm_password", "ienterprisecrm_password_sanitize" );
	
	$id = "ienterprisecrm_recaptcha";
	$title = "Enable reCAPTCHA";
	$callback = "ienterprisecrm_recaptcha_field";
	$page = "ienterprisecrm";
	$section = 'ienterprisecrm_section';
	add_settings_field ( $id, $title, $callback, $page, $section, $args = array () );
	register_setting ( "ienterprisecrm", "ienterprisecrm_recaptcha" );
	
	$id = "ienterprisecrm_recaptcha_key";
	$title = "Your reCAPTCHA key";
	$callback = "ienterprisecrm_recaptcha_key_field";
	$page = "ienterprisecrm";
	$section = 'ienterprisecrm_section';
	add_settings_field ( $id, $title, $callback, $page, $section, $args = array () );
	register_setting ( "ienterprisecrm", "ienterprisecrm_recaptcha_key" );
	
	$id = "ienterprisecrm_recaptcha_secret";
	$title = "Your reCAPTCHA secret";
	$callback = "ienterprisecrm_recaptcha_secret_field";
	$page = "ienterprisecrm";
	$section = 'ienterprisecrm_section';
	add_settings_field ( $id, $title, $callback, $page, $section, $args = array () );
	register_setting ( "ienterprisecrm", "ienterprisecrm_recaptcha_secret" );
	
	$id = "ienterprisecrm_submitbtn";
	$title = "Submit Button label";
	$callback = "ienterprisecrm_submitbtn_field";
	$page = "ienterprisecrm";
	$section = 'ienterprisecrm_section';
	add_settings_field ( $id, $title, $callback, $page, $section, $args = array () );
	register_setting ( "ienterprisecrm", "ienterprisecrm_submitbtn" );
}
function ienterprisecrm_settings_section() {
}
function ienterprisecrm_endpoint_field() {
	echo '<input name="ienterprisecrm_endpoint" id="ienterprisecrm-endpoint" type="hidden" value="' . get_option ( 'ienterprisecrm_endpoint' ) . '" class="large-text" placeholder="URL" required/> ';
}
function ienterprisecrm_username_field() {
	echo '<input name="ienterprisecrm_username" id="ienterprisecrm-username" type="email" value="' . get_option ( 'ienterprisecrm_username' ) . '" class="regular-text" placeholder="Username" required/> ';
}
function ienterprisecrm_password_field() {
	echo '<input name="ienterprisecrm_password" id="ienterprisecrm-password" type="password" value="' . get_option ( 'ienterprisecrm_password' ) . '" class="regular-text" placeholder="Password" required /> ';
}
function ienterprisecrm_recaptcha_field() {
	echo '<input name="ienterprisecrm_recaptcha" id="ienterprisecrm-recaptcha" type="checkbox" ' . (get_option ( 'ienterprisecrm_recaptcha' ) ? 'checked' : '') . ' placeholder="reCAPTCHA" /> ';
}
function ienterprisecrm_recaptcha_key_field() {
	echo '<input name="ienterprisecrm_recaptcha_key" id="ienterprisecrm-recaptcha-key" type="text" value="' . get_option ( 'ienterprisecrm_recaptcha_key' ) . '" class="regular-text" placeholder="reCAPTCHA Key"/> ';
}
function ienterprisecrm_recaptcha_secret_field() {
	echo '<input name="ienterprisecrm_recaptcha_secret" id="ienterprisecrm-recaptcha-secret" type="text" value="' . get_option ( 'ienterprisecrm_recaptcha_secret' ) . '" class="regular-text" placeholder="reCAPTCHA Secret"/> ';
}
function ienterprisecrm_submitbtn_field() {
	echo '<input name="ienterprisecrm_submitbtn" id="ienterprisecrm-submitbtn" type="text" value="' . get_option ( 'ienterprisecrm_submitbtn' ) . '" class="regular-text" placeholder="Submit Button label" /> ';
}
function ienterprisecrm_endpoint_sanitize($input) {
	if (! $input) {
		add_settings_error ( "ienterprisecrm_endpoint", "ienterprisecrm-endpoint", "Invalid URL!" );
		update_option ( 'ienterprisecrm_is_connected', false );
	}
	return $input;
}
function ienterprisecrm_password_sanitize($input) {
	//update_option ( 'ienterprisecrm_endpoint', 'http://localhost/iCRM' );
	$url = rtrim ( get_option ( 'ienterprisecrm_endpoint' ), '/' ) . "/rest/v1/user";
	$responseData = ienterprisecrm_calliCRMAPI ( $url, get_option ( 'ienterprisecrm_username' ), $input );
	if ($responseData ['body']) {
		$responseDecoded = json_decode ( json_encode ( json_decode ( $responseData ['body'] ) ), true );
	}
	if ($responseData ['response'] ['code'] == 200 && $responseDecoded ['id']) {
		// Get Reg fields
		$url = rtrim ( get_option ( 'ienterprisecrm_endpoint' ), '/' ) . "/rest/v1/modules/registrations";
		$fieldsResponseData = ienterprisecrm_calliCRMAPI ( $url, get_option ( 'ienterprisecrm_username' ), $input );
		$fieldsResponseDecoded = json_decode ( json_encode ( json_decode ( $fieldsResponseData ['body'] ) ), true );
		update_option ( 'ienterprisecrm_fields', $fieldsResponseDecoded ['registrations'] ['fields'] );
		// Get keywords
		$url = rtrim ( get_option ( 'ienterprisecrm_endpoint' ), '/' ) . "/rest/v1/config";
		$configResponseData = ienterprisecrm_calliCRMAPI ( $url, get_option ( 'ienterprisecrm_username' ), $input );
		$configResponseDecoded = json_decode ( json_encode ( json_decode ( $configResponseData ['body'] ) ), true );
		update_option ( 'ienterprisecrm_keywords', $configResponseDecoded ['keywords'] );
		// Captcha key
		if (get_option ( 'ienterprisecrm_recaptcha' )) {
			update_option ( 'ienterprisecrm_recaptcha_key', '6Lej0CgUAAAAAFxnP7EzGEARakLWlo9hCAz23fVL' );
		}
		// Success message
		$msg = "Hi " . $responseDecoded ['fullname'] . ", ";
		$msg .= "Settings saved and Successfully connected to iEnterprise CRM.";
		add_settings_error ( "ienterprisecrm_endpoint", "ienterprisecrm-endpoint", $msg, 'updated' );
		update_option ( 'ienterprisecrm_is_connected', true );
	} else {
		add_settings_error ( "ienterprisecrm_username", "ienterprisecrm-username", $responseDecoded ['error'] ['message'] ?: 'Unable to Connect to iEnterprise CRM' );
		update_option ( 'ienterprisecrm_is_connected', false );
	}
	return $input;
}
function ienterprisecrm_calliCRMAPI($service_url, $username, $password) {
	$response = wp_remote_get ( $service_url, array (
			'headers' => array (
					'Authorization' => 'Basic ' . base64_encode ( $username . ":" . $password ) 
			) 
	) );
	return $response;
}