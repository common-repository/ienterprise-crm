<?php
if (! defined ( 'ABSPATH' )) {
	exit (); // Exit if accessed directly
}
?>

<!-- Title -->
<h4 style="color: #333333;"><?= $a['title'] ?></h4>

<!-- Success Message -->
<span id="ienterprisecrm-success-result" style="display: none;"></span>

<div class="regpanel">
	<form id="ienterprisecrm-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
		<div class="main-center">
			<?php wp_nonce_field('ienterprisecrm_submitted'); ?>
	        <input name="action" value="ienterprisecrm_submitted" type="hidden">
	        <?php if (isset($a['redirect_url'])) { ?>
	            <input type="hidden" name="ienterprisecrm_redirect" value="<?php echo $a['redirect_url'] ?>" />
	        <?php } ?>
			
			<!-- Hidden fields-->
			<input type="hidden" value="<?php echo $_SERVER["REMOTE_ADDR"]; ?>" id="remotip" /> <input type="hidden" value="<?php echo IENTERPRISECRM_PLUGIN_URL; ?>" id="url" />

			<div class="row">
				<div class="col-md-6">
					<?php $colposition = 1; include "fields.php"; ?>
				</div>
				<div class="col-md-6">
					<?php $colposition = 2; include "fields.php"; ?>
				</div>
			</div>

			<?php include "custom.php"; ?>

			<br>

			<div class="row" style="height: 70px; overflow: hidden;">
				<?php
				if (get_option ( 'ienterprisecrm_recaptcha' )) {
					?>
				<div class="col-md-6">
					<!-- Google Hidden ReCapcha-->
					<div>
						<div class="g-recaptcha" data-theme="light" data-size="normal" data-sitekey="<?php echo get_option ( 'ienterprisecrm_recaptcha_key' ); ?>" style="transform: scale(0.75); -webkit-transform: scale(0.75); transform-origin: 0 0; -webkit-transform-origin: 0 0;"></div>
					</div>
				</div>
				<?php
				}
				?>
				<div class="col-md-6">
					<!-- Error Message -->
					<span id="ienterprisecrm-error-message" style="display: none;"></span>
				</div>
			</div>

			<div class="text-center">
				<button type="submit" id="ienterprisecrm-submit" class="btn btn-primary btn-md login-button" style="width: 100%;"><?php echo (get_option ( 'ienterprisecrm_submitbtn' ) ?: 'Submit'); ?></button>
				<button type="button" id="ienterprisecrm-progress" class="btn btn-default btn-md" style="width: 100%; cursor: progress; display: none;">Please wait...</button>
			</div>
			<br>
		</div>

		<!-- Hidden fields to send to iCRM-->
		<input id="ReferrerUrl" name="ReferrerUrl" type="hidden" value="<?php echo $_SESSION ['org_referer']; ?>"> 
		<input id="CampaignId" name="CampaignId" type="hidden" value="<?php echo @$a['campaign_id'] ?: @$a['utm_campaign']; ?>"> 
		<input id="LandingPage" name="LandingPage" type="hidden" value="<?php echo $_SESSION ['org_regpath']; ?>"> 
		<input id="ClientIP" name="ClientIP" type="hidden" value="<?php echo getenv ( 'HTTP_CLIENT_IP' ) ?: getenv ( 'HTTP_X_FORWARDED_FOR' ) ?: getenv ( 'HTTP_X_FORWARDED' ) ?: getenv ( 'HTTP_FORWARDED_FOR' ) ?: getenv ( 'HTTP_FORWARDED' ) ?: getenv ( 'REMOTE_ADDR' ); ?>">

	</form>
</div>
<br>