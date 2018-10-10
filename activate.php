<?php
/**
 * Configuración inicial.
 * 
 * Se ejecuta unicamente a la activación del plugin y obtiene el ultimo id 
 * de la table de "elgg_events_log".
 * Crea si no existe la tabla "elgg_lrs_import" y configura la obtencion 
 * de las cabeceras.
 * 
 * @author Adolfo del Sel Llano y Victor Corchero Morais
 * @version Elgg 1.10
 * @package lrs_export_import
 */
require_once __DIR__ . '/lib/functions.php';
$result = DB_select(8,0);
elgg_save_config("id_db",$result[0]->id);

$dbprefix = elgg_get_config('dbprefix');
    $sql = "CREATE TABLE IF NOT EXISTS `{$dbprefix}lrs_import` (";
    $sql .= "`id` int(11) NOT NULL AUTO_INCREMENT,";
    $sql .= "`web` varchar(30) NOT NULL,";
    $sql .= "`platform` varchar(15) NOT NULL,";
    $sql .= "`actor_guid` int(11) NOT NULL,";
    $sql .= "`action_type` varchar(15) NOT NULL,";
    $sql .= "`resource_type` varchar(20) NOT NULL,";
    $sql .= "`object_name` varchar(30) NOT NULL,";
    $sql .= "`url_object` TINYTEXT NOT NULL,";
    $sql .= "`url_parent` TINYTEXT NOT NULL,";
    $sql .= "`group_name` TINYTEXT NOT NULL,";
    $sql .= "`group_id` int(11) NOT NULL,";
    $sql .= "`time_created` DATETIME NOT NULL,";
    $sql .= "PRIMARY KEY (`id`)";
    $sql .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    update_data($sql);


//FUNCION APACHE_REQUEST_HEADERS PARA SERVIDORES NGINX
if (!function_exists('apache_request_headers')) { 
        function apache_request_headers() { 
            foreach($_SERVER as $key=>$value) { 
                if (substr($key,0,5)=="HTTP_") { 
                    $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5))))); 
                    $out[$key]=$value; 
                }else{ 
                    $out[$key]=$value; 
        } 
            } 
            return $out; 
        } 
}