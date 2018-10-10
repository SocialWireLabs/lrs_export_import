<?php
/**
 * Vista que ve el administrador en "Configuración->LRS Export Import".
 * 
 * En esta vista se puede ajustar el tiempo para exportar los datos
 * a los LRS.
 *   
 * @author Adolfo del Sel Llano y Victor Corchero Morais
 * @version Elgg 1.10
 * @package lrs_export_import
 * @subpackage views/default/plugins/lrs_export_imoprt
 */
$period = $vars['entity']->period;
$entity = elgg_extract('entity', $vars);
if (!$period) {
	$period = 'minute';
}

?>

<div>
		<?php
			echo elgg_echo('LRS_Export_Import:period') . ' ';
			echo elgg_view('input/select', array(
													'name'           => 'params[period]',
													'options_values' => array(
																				'minute'     => elgg_echo('LRS_Export_Import:minute'),
																				'fiveminute' => elgg_echo('LRS_Export_Import:fiveminute'),
																				'fifteenmin' => elgg_echo('LRS_Export_Import:fifteenmin'),
																				'halfhour'   => elgg_echo('LRS_Export_Import:halfhour'),
																				'daily'      => elgg_echo('LRS_Export_Import:daily'),
																				'weekly'     => elgg_echo('LRS_Export_Import:weekly'),
																				'monthly'    => elgg_echo('LRS_Export_Import:monthly'),
																				'yearly'     => elgg_echo('LRS_Export_Import:yearly'),
																			),
													'value'          => $period,
												));
		?>
</div>

<?php
$delete = $vars['entity']->delete;
$entity = elgg_extract('entity', $vars);
if (!$delete) {
	$delete = 'never';
}

?>

<div>
		<?php
			echo elgg_echo('LRS_Export_Import:delete') . ' ';
			echo elgg_view('input/select', array(
													'name'           => 'params[delete]',
													'options_values' => array(
																				'weekly'     => elgg_echo('LRS_Export_Import:weekly'),
																				'monthly'    => elgg_echo('LRS_Export_Import:monthly'),
																				'yearly'     => elgg_echo('LRS_Export_Import:yearly'),
																				'never'     => elgg_echo('LRS_Export_Import:never'),
																			),
													'value'          => $delete,
												));
		?>
</div>