<?php
if (! defined ( 'ABSPATH' )) {
	exit (); // Exit if accessed directly
}

add_action ( 'admin_menu', 'ienterprisecrm_admin_menu' );
function ienterprisecrm_admin_menu() {
	add_options_page ( 'iEnterprise CRM', 'iEnterprise CRM', 'manage_options', 'ienterprisecrm', 'ienterprisecrm_options_page' );
}
function ienterprisecrm_options_page() {
	$fields = get_option ( 'ienterprisecrm_fields' );
	$keywords = get_option ( 'ienterprisecrm_keywords' );
	?>
<div class="wrap">
	<h2>iEnterprise CRM Connection Settings</h2>
	<hr>
	<b>
		<span style="color: red;">Not an iEnterprise CRM customer? </span>
		<a style="color: blue;" href="https://ienterprises.com/products/ienterprise-crm/?campaign_id=WordpressStore01&product=EmpowerCRM&data_source=NA&lead_source=Other&trial=1&email_application=other" target="_blank">
			Click here to sign-up for free.
		</a>
	</b>
	<hr>
	<form method="POST" action="options.php">
            <?php
	settings_fields ( 'ienterprisecrm' );
	do_settings_sections ( 'ienterprisecrm' );
	submit_button ();
	?>
        </form>

	<h3>Status</h3>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">Connected</th>
				<td><strong><?php echo get_option('ienterprisecrm_is_connected') ? 'Yes' : 'No' ?></strong></td>
			</tr>
		</tbody>
	</table>

	<h3>Instructions</h3>
	<ul>
		<li>- To display the form in any of your pages or posts just paste the shortcode
		<code>[ienterprisecrm-form title="Your Form Title"]</code>
		in the content.</li>
		<li>- Include arguments - <code>[field name in lowercase]="Value"</code> to default any field values.</li>
		<li>- You can also default field values by passing as URL arguments - <code>[field name in lowercase]=Value</code></li>
	</ul>

</div>
<?php
}
