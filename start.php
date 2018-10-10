<?php
require_once __DIR__ . '/lib/functions.php';
use TinCan\Statement as TinCanStatement;
require_once __DIR__ ."/vendor/autoload.php";

elgg_register_event_handler('init', 'system', 'lrs_export_import_init');
/**
 * Inicio del plugin "lrs_export_import".
 * 
 * Registramos las acciones, los hooks y los handler necesarios.
 * Cabe destacar que el periodo que se usa en el cron es configurable por parte
 * del administrador de la plataforma.
 * La configuracion necesaria para el crontab de Linux está especificada en el 
 * archivo leeme.txt.
 * 
 * @global string dirname(__FILE__)
 * @author Adolfo del Sel Llano y Victor Corchero Morais
 * @version Elgg 1.10
 * @package lrs_export_import
 */
function lrs_export_import_init() {

    $root = dirname(__FILE__);
    $action_path = "$root/lrs_export_import/usersettings";
	$period = elgg_get_plugin_setting('period', 'lrs_export_import');
    $delete = elgg_get_plugin_setting('delete', 'lrs_export_import');
    
    elgg_register_action('lrs_export_import/usersettings/save', "$action_path/save.php");
	
    elgg_register_plugin_hook_handler('cron', $period, 'lrs_export_import_export_cron');
    elgg_register_plugin_hook_handler('cron', $delete, 'lrs_export_import_delete_cron');

    elgg_register_page_handler('lrs_export_import', 'lrs_export_import_page_handler');

    elgg_register_event_handler('pagesetup', 'system', 'lrs_export_import_url');
}
/**
 * Exportación llamada por el cron.
 * 
 * Obtenemos todos los eventos que ocurrieron durante el periodo
 * del cron. Es útil hacerlo de esta forma porque así el usuario no se ve perjudicado
 * por el envio de datos a los LRS.
 * Envia cada evento por separado a la función "config_tincan" para que sea tratado.
 * Por último guarda el último id tratado para saber hasta donde llegó y no tratar 
 * los mismos eventos varias veces.
 * Cabe destacar que se necesitan ignorar los permisos de elgg temporalmente para 
 * poder tratar todo lo relacionado con las preguntas "elgg_set_ignore_access".
 * 
 * @author Adolfo del Sel Llano y Victor Corchero Morais
 * @version Elgg 1.10
 * @package lrs_export_import
 */
function lrs_export_import_export_cron () {    

    elgg_set_ignore_access($ignore = true);

    $dbprefix = elgg_get_config('dbprefix');
    $aux      = 0;

    $id_saved      = elgg_get_config("id_db");
    $result        = DB_select(0,$id_saved);
    $len           = count($result);
    
    foreach ($result as $entry) {
        if (!empty($entry->id)) {
            config_tincan($entry->id);
            if($aux == $len - 1){
                elgg_save_config("id_db",$entry->id);
            }
        }
        $aux++;
    }
}
/**
 * Definición de actividades y verbos.
 * 
 * Gestionamos las defeniciones de verbos y actividades obtenidas
 * de las urls de "verb" y "object".
 * Ademas en el tercer caso, "import", se capturan las cabeceras para qeu se 
 * puedan procesar los datos a importar.
 * 
 * @param array $page
 * @param string $identifier 
 * @author Adolfo del Sel Llano y Victor Corchero Morais
 * @version Elgg 1.10
 * @package lrs_export_import
 */
function lrs_export_import_page_handler($page, $identifier) {

    switch ($page[0]) {
        case 'verbs' :
                        set_input('verb', $page[1]);
                        echo elgg_view('tincan/verbs');
                        return true;
        case 'activities' :
                            set_input('activity', $page[1]);
                            echo elgg_view('tincan/activities');
                            return true;
        case 'import' :
                        $cabeceras = apache_request_headers();
                        import_tincan($cabeceras,$page[1]);
                        break;
        case 'url_export_import' :
                elgg_group_gatekeeper();
                elgg_set_page_owner_guid($page[1]);
                $content = elgg_view('lrs_export_import/url_export_import', $vars);
                $title = "Utilizar un LRS para tu grupo";
                $params = array(
                    'content' => $content,
                    'title' => $title,
                    'filter' => '',
                );

                $body = elgg_view_layout('content',$params);
                echo elgg_view_page($title,$body);
                break;     
        default:
                echo "request for $identifier $page[0]";
                break;
    }
    return true;
}

/**
 * Control de cache.
 * 
 * Gestionamos la cantidad de información almacenada en la DB.
 * El adminstrador podra escoger varias tiempos de "limpiado" de eventos.
 * Esta funcion es la encargada de elminar las entradas mas antguas de 
 * la tabla "elgg_lrs_import".
 * 
 * @param array $entity_type Indica el tiempo de borrado.
 * @author Adolfo del Sel Llano y Victor Corchero Morais
 * @version Elgg 1.10
 */
function lrs_export_import_delete_cron($hook, $entity_type, $returnvalue, $params) {

    $day    = 86400; //seconds
    $offset = 0;
    $period = $entity_type;
    switch ($period) {
        case 'weekly':
                        $offset = $day * 7;
                        break;
        case 'yearly':
                        $offset = $day * 365;
                        break;
        case 'monthly':
                        $offset = $day * 28;
                        break;       
    }

    $flag = lrs_export_import_delete_log($offset);
    if($flag){
        elgg_log("Se pudo borrar los datos",'ERROR');
    }else{
        elgg_log("No se pudo borrar los datos, la tabla quedaria vacia",'ERROR');
    }
}
/**
 * Composicion de url par el envio de datos al lrs.
 * 
 * Con esta función mostramos al profesor del grupo la url necesaria para 
 * que le envien los datos, al lrs interno, y que sean asociados a ese grupo.
 * 
 * @author Adolfo del Sel Llano y Victor Corchero Morais
 * @version Elgg 1.10
 */
function lrs_export_import_url() {
       $page_owner = elgg_get_page_owner_entity();
       $guid = $page_owner->guid;
       $user = elgg_get_logged_in_user_entity();
   if (elgg_is_logged_in() && (events_collector_is_logged_in_group_admin($guid) || $user->isAdmin())) {
       if (elgg_in_context('groups')) {
           if ($page_owner instanceof ElggGroup && $page_owner->canEdit()) {
               elgg_register_menu_item('page', array(
                   'href' => '/lrs_export_import/url_export_import/' . $page_owner->guid,
                   'name' => 'url_lrs',
                   'text' => "Utilizar un LRS",
               ));
           }
       }
   }
} 