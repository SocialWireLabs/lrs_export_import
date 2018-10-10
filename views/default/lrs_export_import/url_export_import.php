<?php
/**
 * URL_export_import
**/
$page_owner = elgg_get_page_owner_entity();

if ($page_owner instanceof ElggGroup) {
	$title = elgg_echo("Obtener URL del LRS para tu grupo: %s", array($page_owner->name));
}

$guid = $page_owner->getGUID();

if (!$name && ($user = elgg_get_logged_in_user_entity())) {
	$name = $user->username;
}

$url = elgg_get_site_url();
$img = elgg_view('output/img', array(
	'src' => 'mod/lrs_export_import/assets/Tin-Can_API.jpg',
	'alt' => $title,
));
$url_export_import = "<b>" . elgg_get_site_url() . "lrs_export_import/import/" .$page_owner->guid . "</b>";

?>
<p><?php echo elgg_echo("LRS_Export_Import:lrs_explain"); ?></p>
<p><?php echo elgg_echo("LRS_Export_Import:lrs_details"); ?></p>
<p><?php echo $img ?></p>
<p><?php echo elgg_echo("LRS_Export_Import:lrs_import", array($page_owner->name)); ?></p>
<p><?php echo $url_export_import; ?></p>
<p><?php echo elgg_echo("LRS_Export_Import:lrs_export"); ?></p>
