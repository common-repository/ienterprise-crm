<?php
if (! defined ( 'ABSPATH' )) {
	exit (); // Exit if accessed directly
}

add_shortcode ( 'ienterprisecrm-form', 'ienterprisecrm_form' );
add_action ( 'wp_ajax_ienterprisecrm_submitted', 'process_ienterprisecrm_form' );
add_action ( 'wp_ajax_nopriv_ienterprisecrm_submitted', 'process_ienterprisecrm_form' );
function ienterprisecrm_form($atts) {
	if (! get_option ( 'ienterprisecrm_is_connected' )) {
		return '<code>iEnterprise CRM is not connected. Please configure the plugin in the admin settings.</code>';
	}
	
	session_start ();
	
	// Save referer
	if ($_SESSION ['org_referer'] == '') {
		$_SESSION ['org_referer'] = $_SERVER ['HTTP_REFERER'];
	}
	
	// Save original reg path
	if ($_SESSION ['org_regpath'] == '') {
		$_SESSION ['org_regpath'] = $_SERVER ['REQUEST_URI'];
	}
	
	$args = array (
			'title' => 'Registration',
			'firstname' => '' 
	);
	$a = shortcode_atts ( $args, $atts );
	
	wp_enqueue_script ( 'ienterprisecrm-form', IENTERPRISECRM_PLUGIN_URL . '/js/form.js', array (
			'jquery' 
	), '1.0.7', true );
	
	wp_enqueue_script ( 'ienterprisecrm-google-recapcha', 'https://www.google.com/recaptcha/api.js' );
	
	wp_enqueue_style ( 'ienterprisecrm-style', IENTERPRISECRM_PLUGIN_URL . '/css/ienterprisecrm.css', array (), '1.0.7' );
	
	wp_enqueue_style ( 'ienterprisecrm-bootstrap-style', IENTERPRISECRM_PLUGIN_URL . '/lib/css/bootstrap.min.css' );
	
	wp_enqueue_style ( 'ienterprisecrm-fontawesome-style', IENTERPRISECRM_PLUGIN_URL . '/lib/font-awesome/css/font-awesome.min.css' );
	
	// If add url with campaign id - save params
	if ($_GET ['campaign_id'] != '' || $_GET ['utm_campaign'] != '') {
		foreach ( $_GET as $key => $arg ) {
			$key = strtolower ( $key );
			$_SESSION ['SavedParamps'] [$key] = $arg;
		}
	}
	
	// Get values from url params
	foreach ( $_GET as $key => $arg ) {
		$key = strtolower ( $key );
		$a [$key] = $arg;
	}
	
	// Get saved params from ad url
	if (isset ( $_SESSION ['SavedParamps'] )) {
		foreach ( $_SESSION ['SavedParamps'] as $key => $arg ) {
			$key = strtolower ( $key );
			if ($a [$key] == '') {
				$a [$key] = $arg;
			}
		}
	}
	
	ob_start ();
	include IENTERPRISECRM_PLUGIN_DIR . '/views/form.php';
	return ob_get_clean ();
}
/* **************************************** New registration ************************************************* */
function process_ienterprisecrm_form() {
	if (empty ( $_POST ) || ! wp_verify_nonce ( $_POST ['_wpnonce'], 'ienterprisecrm_submitted' ) || ! get_option ( 'ienterprisecrm_is_connected' )) {
		// echo 'You targeted the right function, but sorry, your nonce did not verify.';
	} else {
		$comments = "";
		
		$endpoint = get_option ( 'ienterprisecrm_endpoint' );
		$service_url = rtrim ( $endpoint, '/' ) . "/rest/v1/registrations";
		
		$username = get_option ( 'ienterprisecrm_username' );
		$password = get_option ( 'ienterprisecrm_password' );
		
		$curl_post_data = array (
				'type' => 'registrations',
				'RegistrationIP' => $_POST ['ClientIP'],
				'ReferrerUrl' => $_POST ['ReferrerUrl'],
				'CampaignId' => $_POST ['CampaignId'],
				'LandingPage' => $_POST ['LandingPage'] 
		);
		
		$fields = array_values ( array_filter ( get_option ( 'ienterprisecrm_fields' ), function ($arrayValue) use ($colposition) {
			return $arrayValue ['onscreen'] && ! $arrayValue ['readonly'] && ($arrayValue ['type'] == 'string' || $arrayValue ['type'] == 'text' || $arrayValue ['type'] == 'email' || $arrayValue ['type'] == 'phone' || $arrayValue ['type'] == 'integer' || $arrayValue ['type'] == 'picklist');
		} ) );
		
		foreach ( $fields as $field ) {
			if ($field ['required'] && ! $_POST [$field ['name']]) {
				$returnData = [ 
						'result' => 'danger',
						'message' => $field ['label'] . ' is required !',
						'error' => '1' 
				];
				echo json_encode ( $returnData );
				exit ();
			}
			$fieldVal = $_POST [$field ['name']];
			if (is_array ( $fieldVal )) {
				$fieldVal = implode ( ',', $fieldVal );
			}
			$curl_post_data [$field ['name']] = sanitize_text_field ( $fieldVal );
		}
		
		$curl_post_data = ienterprisecrm_addCustomFieldsForRegistration ( $curl_post_data );
		
		// Google reCaptcha check
		if (get_option ( 'ienterprisecrm_recaptcha' )) {
			$capchaFailed = false;
			if (isset ( $_POST ['g-recaptcha-response'] )) {
				$capchaValidation = ienterprisecrm_callCapchaAPI ( $_REQUEST ['g-recaptcha-response'], $_SERVER ["REMOTE_ADDR"] );
				$capchaValidationDecoded = json_decode ( json_encode ( json_decode ( $capchaValidation ['body'] ) ), true );
				if (! $capchaValidationDecoded ['success']) {
					$capchaFailed = true;
				}
			} else {
				$capchaFailed = true;
			}
			if ($capchaFailed) {
				if (defined ( 'DOING_AJAX' ) && DOING_AJAX) {
					echo json_encode ( [ 
							'result' => 'danger',
							'message' => 'reCAPTCHA verification failed!' 
					] );
					exit ();
				}
			}
		}
		
		$service_url = $service_url . '?validationlevel=system';
		
		$responseData = ienterprisecrm_calliCRMAPI2 ( $service_url, $curl_post_data, $username, $password );
		$responseDecoded = json_decode ( json_encode ( json_decode ( $responseData ['body'] ) ), true );
		
		$status = $responseData ['response'] ['code'];
		if ($status != '200' && $status != '201') {
			$returnData = [ 
					'result' => 'danger',
					'message' => 'Error occured: ' . $responseDecoded->error->message . '(' . $status . ')',
					'error' => '1' 
			];
		} else {
			
			$customSuccessMessage = ienterprisecrm_customFunctionAfterRegistration ();
			
			$returnData = [ 
					'result' => 'success',
					'error' => '0',
					'message' => "Thank you for your interest. We will be in touch...<br>" . $customSuccessMessage,
					'redirect' => isset ( $_POST ['ienterprisecrm_redirect'] ) ? $_POST ['ienterprisecrm_redirect'] : false 
			];
		}
		
		if (defined ( 'DOING_AJAX' ) && DOING_AJAX) {
			echo json_encode ( $returnData );
			exit ();
		}
	}
	
	wp_redirect ( home_url () );
	exit ();
}
function ienterprisecrm_calliCRMAPI2($service_url, $curl_post_data, $username, $password) {
	$response = wp_remote_post ( $service_url, array (
			'headers' => array (
					'Authorization' => 'Basic ' . base64_encode ( $username . ":" . $password ) 
			),
			'body' => json_encode ( $curl_post_data ) 
	) );
	return $response;
}
function ienterprisecrm_callCapchaAPI($CaptchaResponse, $remoteIp) {
	$response = wp_remote_post ( 'https://www.google.com/recaptcha/api/siteverify', array (
			'body' => array (
					'secret' => get_option ( 'ienterprisecrm_recaptcha_secret' ),
					'response' => $CaptchaResponse,
					'remoteip' => $remoteIp 
			) 
	) );
	return $response;
}
include "custom.php";