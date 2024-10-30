<!-- $colposition passed from parent php file -->
<?php
if (! defined ( 'ABSPATH' )) {
	exit (); // Exit if accessed directly
}

$fields = array_values ( array_filter ( get_option ( 'ienterprisecrm_fields' ), function ($arrayValue) use ($colposition) {
	return $arrayValue ['colposition'] == $colposition && $arrayValue ['onscreen'] && ! $arrayValue ['readonly'] && ($arrayValue ['type'] == 'string' || $arrayValue ['type'] == 'text' || $arrayValue ['type'] == 'email' || $arrayValue ['type'] == 'phone' || $arrayValue ['type'] == 'integer' || $arrayValue ['type'] == 'picklist');
} ) );
$keywords = get_option ( 'ienterprisecrm_keywords' );
foreach ( $fields as $field ) {
	?>
<div class="form-group">
	<span class="form-field-label"><?php echo $field['label'];?> : </span> <span style="color: red; font-size: 9px;"><?php echo ($field ['required']?'<i class="fa fa-flag"></i>':''); ?></span>
				<?php
	switch ($field ['type']) {
		case 'string' :
		case 'text' :
		case 'email' :
		case 'phone' :
			?>
	<input type="text" class="form-control" name="<?php echo $field['name']; ?>" id="<?php echo $field['name']; ?>" <?php echo ($field ['required'] ? 'required' : ''); ?> value="<?php echo @$a[strtolower($field['name'])]; ?>" placeholder="<?php echo $field['label']; ?>" />
	<?php
			break;
		case 'integer' :
			?>
				<input type="number" class="form-control" name="<?php echo $field['name']; ?>" id="<?php echo $field['name']; ?>" <?php echo ($field ['required'] ? 'required' : ''); ?> value="<?php echo @$a[strtolower($field['name'])]; ?>" placeholder="<?php echo $field['label']; ?>" />
						<?php
			break;
		case 'picklist' :
			if ($field ['multiple']) {
				if (is_array ( $keywords )) {
					echo '<br>';
					foreach ( $keywords [$field ['keywordid']] ['options'] as $keyword ) {
						?>
                            <input type="checkbox" id="<?php echo $field['name'].'-'.$keyword['id']; ?>" name="<?php echo $field['name']; ?>[]" value="<?php echo $keyword['id']; ?>" <?php echo strcasecmp(@$a[strtolower($field['name'])], $keyword['id']) == 0 ? 'checked' : '' ?>>&nbsp;<?php echo $keyword['label']; ?>&nbsp;&nbsp;&nbsp;
                        <?php
					}
				}
			} else {
				?>
						<select class="form-control" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" <?php echo ($field ['required'] ? 'required' : ''); ?> placeholder="P<?php echo $field['label']; ?>">
		<option value=''>- Select <?php echo $field['label']; ?> -</option>
					    <?php
				if (is_array ( $keywords )) {
					foreach ( $keywords [$field ['keywordid']] ['options'] as $keyword ) {
						?>
                            <option value="<?php echo $keyword['id']; ?>" <?php echo strcasecmp(@$a[strtolower($field['name'])], $keyword['id']) == 0 ? 'selected="selected"' : '' ?>><?php echo $keyword['label']; ?></option>
                        <?php
					}
				}
				?>
						</select>
						<?php
			}
			break;
	}
	?>
				</div>
<?php
}
?>