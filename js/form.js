jQuery(document).ready(function() {

    // Screen 1 submit function
    jQuery("#ienterprisecrm-form").submit(function(e) {
	e.preventDefault();

	jQuery("#ienterprisecrm-success-result").html('');
	jQuery("#ienterprisecrm-error-message").html('');

	jQuery("#ienterprisecrm-submit").hide();
	jQuery("#ienterprisecrm-progress").show();

	var form = jQuery(this);

	var remoteIp = jQuery('#remotip').val();
	var dataput = jQuery(this).serialize();

	jQuery.post(form.attr('action'), dataput, function(data) {
	    // console.log(data);
	    var response = jQuery.parseJSON(data);

	    // console.log(response);
	    if (response.result == 'success') {
		// console.log(response);
		jQuery("#ienterprisecrm-success-result").html('<div class="alert alert-' + response.result + '" role="alert">' + response.message + '</div>');
		jQuery(".regpanel").fadeOut(1000, function() {
		    jQuery("#ienterprisecrm-success-result").fadeIn(1000);
		});
	    } else {
		jQuery("#ienterprisecrm-error-message").html('<div class="alert alert-' + response.result + '" role="alert">' + response.message + '</div>');
		jQuery("#ienterprisecrm-error-message").fadeIn(500);
		jQuery("#ienterprisecrm-error-message").fadeOut(1500, function() {
		    jQuery("#ienterprisecrm-progress").hide();
		    jQuery("#ienterprisecrm-submit").show();
		});
	    }

	    if (response.redirect) {
		window.location.href = response.redirect;
	    }
	});

    });

});
