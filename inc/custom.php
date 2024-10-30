<?php
if (! defined ( 'ABSPATH' )) {
	exit (); // Exit if accessed directly
}

// Function to send additional fields to iCRM registration
// Return modified $curl_post_data
function ienterprisecrm_addCustomFieldsForRegistration($curl_post_data) {
	return $curl_post_data;
}
// Function that run after succesfull registration
// Return message
function ienterprisecrm_customFunctionAfterRegistration() {
	return '';
}