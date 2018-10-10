<?php
/**
 * Captura y guarda la configuración de usuario.
 * 
 * Toma los datos de la configuración del lrs_export_import 
 * propia de cada usuario y los guarda como metadatos 
 * de dicho usuario.
 * 
 * @author Adolfo del Sel Llano y Victor Corchero Morais
 * @version Elgg 1.10
 * @package lrs_export_import
 * @subpackage lrs_export_import/ussersettings
 */
$usuario  = elgg_get_logged_in_user_entity();
$i        = 0;
$variable = get_input('opciones_subida');
$array    = array();
$last     = 'string';
foreach ($variable as $key => $value) {
	if(gettype($value) != 'array'){
		if($i!=0){
			if($last!='array')
				$metadataname           = 'opciones_subida_'.($i-1);
				$usuario->$metadataname = $array;
				$array                  = array();
				$metadataname           = 'opciones_subida_'.($i);
		}
		$i = $i+1;
	}	
		if (gettype($value) == 'array'){
			foreach ($value as $key2 => $value2) {
				$metadataname = 'opciones_subida_'.($i-1);
				array_push($array, $value2);
			}
		}
		$last = gettype($value);
}
$usuario->$metadataname = $array;
$usuario->save();

$lrs_username = get_input('lrs_username');
$lrs_password = get_input('lrs_password');
$lrs_endpoint = get_input('lrs_endpoint');
// $aux=$i;
$i=0;
$e=0;
$contador=0;
foreach ($lrs_endpoint as $key => $value) {
	$metadataname = 'opciones_subida_'.$i;
	if(!empty($value) && !empty($lrs_password[$i]) && !empty($lrs_username[$i]) && !empty($usuario->$metadataname) ){
		$contador++;
		$metadataname           = 'lrs_username_'.($i-$e);
		$usuario->$metadataname = $lrs_username[$i];
 		$metadataname           = 'lrs_password_'.($i-$e);
		$usuario->$metadataname = $lrs_password[$i];
		$metadataname           = 'lrs_endpoint_'.($i-$e);
		$usuario->$metadataname = $value;
	}
	else{
		$metadataname = 'lrs_username_'.$i;
		$usuario->deleteMetadata($metadataname);
		$metadataname = 'lrs_endpoint_'.$i;
		$usuario->deleteMetadata($metadataname);
		$metadataname = 'lrs_password_'.$i;
		$usuario->deleteMetadata($metadataname);
		$metadataname = 'opciones_subida_'.$i;
		$usuario->deleteMetadata($metadataname);
		$e++;
	}
	$i++;
}
$o=0;
for ($p=0; $p< $i ; $p++) { 
	$metadataname = 'opciones_subida_'.$p;
	if(empty($usuario->$metadataname)){
		$o++;
	}else{
		$metadataname           = 'opciones_subida_'.$p;
		$value                  = $usuario->$metadataname;
		$metadataname           = 'opciones_subida_'.($p-$o);
		$usuario->$metadataname = $value;
	}
}

//Guardamos como metadatos del usuario para posterior recuperacion
$usuario->count = $contador;
$usuario->save();

//Mensage verde pantalla derecha
$plugin_id   = get_input('plugin_id');
$plugin      = elgg_get_plugin_from_id($plugin_id);
$plugin_name = $plugin->getManifest()->getName();

system_message(elgg_echo('plugins:settings:save:ok', array($plugin_name)));