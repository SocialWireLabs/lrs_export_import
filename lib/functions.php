<?php
use TinCan\Statement as TinCanStatement;
use TinCan\RemoteLRS;
//===========VARIABLES GLOBALES=======//
$user;
$actor;
$verb;
$context;
$object;
$group;
$id;
$name;
$parent;
$endpoint;
$username;
$password;
//===================================//
/**
 * Obtiene las opciones de subida definidas por el usuario y envia 
 * los datos pertinentes.
 * 
 * En esta función se decide en base a los datos configurados por el usuario
 * si se deben enviar los datos a su o sus LRS. 
 * 
 * @global string Url de acceso.
 * @global string Nombre de usuario, el email.
 * @global string Clave de acceso.
 * @param integer $id_Admin Es el id único que le asigna la DB a cada uno de sus datos  .
 * @author Adolfo del Sel Llano y Victor Corchero Morais.
 * @version Elgg 1.10.
 * @package lrs_export_import
 * @subpackage lib
 */
function config_tincan($id_Admin){

    global $endpoint,$username,$password;
    $result = DB_select(1,$id_Admin);
    
    $actor_guid     = $result[0]->actor_guid;
    $action_type    = $result[0]->action_type;
    $resource_type  = $result[0]->resource_type;
    $resource_guid  = $result[0]->resource_guid;
    $tags           = $result[0]->tags;
    $categories     = $result[0]->categories;
    $owner_guid     = $result[0]->owner_guid;
    $container_guid = $result[0]->container_guid;
    $time_created   = $result[0]->time_created;
       
    $user      = get_user($actor_guid);
    $continuar = false;
    
    $count = $user->count;

    $aux = 0;

    for ($aux=0; $aux <$count ; $aux++) { 

        $metadataname          = 'opciones_subida_'.$aux;
        $metadataname_endpoint = 'lrs_endpoint_'.$aux;
        $metadataname_password = 'lrs_password_'.$aux;
        $metadataname_username = 'lrs_username_'.$aux;

        $endpoint     = $user->$metadataname_endpoint;
        $password     = $user->$metadataname_password;
        $username     = $user->$metadataname_username;
               
        if(!empty($user->$metadataname)){
            foreach ((array)$user->$metadataname as $option) {
                        if(strcmp($option,strtolower($action_type))==0){
                            $continuar = true;
                            break;
                        }
                }
        }    
    if($continuar){
        $flag = send_tincan($resource_type,$action_type,$container_guid,$actor_guid,$resource_guid);
        if($flag){
            elgg_log("Se pudo enviar los datos a TinCan [config_tincan]",'ERROR');
        }else{
            elgg_log("No se pudo enviar los datos a TinCan [config_tincan]",'ERROR');
        }
    } 
        $continuar = false;
    }   
}
/**
 * Forma la estructura TinCan y envia los datos.
 * 
 * Esta función permite terner una visión general de la secuencia
 * de ejecución de la parte de "export".
 *  
 * 
 * @global string Nombre de usuario.
 * @param string $resource_type Tipo de archivo (question,file...).
 * @param string $action_type Acción que se realizó (viewed,created...).
 * @param integer $container_guid.
 * @param integer $actor_guid Quien realizó la acción.
 * @param integer $resource_guid Identificador del recurso.
 * @return bool $response Indica si se han realizado correcta o incorrectamente todas las operaciones.
 * @author Adolfo del Sel Llano y Victor Corchero Morais.
 * @version Elgg 1.10.
 * @package lrs_export_import
 * @subpackage lib
 */
function send_tincan($resource_type,$action_type,$container_guid,$actor_guid,$resource_guid){
    try{
        global $user;

        $user = get_entity($actor_guid);
        
        if($actor_guid==0 && $action_type=="LOGGED"){
            $user = get_entity($resource_guid);
        }
        
        set_actor();
        set_verb($action_type);
        set_config_object_context($resource_guid,$container_guid,$resource_type,$action_type);
        
        $response = send();
    }catch(Exception $e){
        return false;
    }
    return $response;
}
/**
 * Configura las opciones de la variable $actor.
 * 
 * Esta función en base a los datos del usurio conforma
 * la variable $actor. 
 *  
 * 
 * @global string Nombre de usuario.
 * @global string Variable de la estructura json que especifica los valores del usuario. 
 * @author Adolfo del Sel Llano y Victor Corchero Morais.
 * @version Elgg 1.10.
 * @package lrs_export_import
 * @subpackage lib
 */
function set_actor (){

    global $user,$actor;

    if(strpos($user->email,'internal.example.com') !== false){
        $actor = array( 'objectType' => 'Agent',
                        'account'    => array(
                                                'homePage' => elgg_get_site_url(), 
                                                'name'     => $user->username
                                            )
                    );
    }else{
         $actor = array(
                        'name'       => $user->username,
                        'mbox'       => 'mailto:'.$user->email,
                        'objectType' => 'Agent',
                    );
    }
}
/**
 * Configura las opciones de la variable $verb.
 * 
 * Esta función, en base al tipo de acción realizada conforma
 * la variable $verb. 
 *  
 * 
 * @global string Variable de la estructura json que especifica los valores del verbo.
 * @param string $action_type Tipo de acción realizada (viewed,uploaded...).
 * @author Adolfo del Sel Llano y Victor Corchero Morais.
 * @version Elgg 1.10.
 * @package lrs_export_import
 * @subpackage lib
 */
function set_verb ($action_type){

    global $verb;
    $verb = array(
                    'id'      => elgg_get_site_url() . "lrs_export_import/verbs/". strtolower($action_type),
                    'display' => array(
                                        'en' => strtolower($action_type),
                                    ),
            );
}
/**
 * Configura las opciones de las variables $object y $context.
 * 
 * Esta función permite terner una visión general de la secuencia
 * de ejecución de la configuración de dichas dos variables.
 *  
 * 
 * @global string Variable de la estructura json que especifica los valores del objeto. 
 * @global string Variable de la estructura json que especifica los valores del grupo al cual perteneces. 
 * @param integer $resource_guid Identificador del recurso.
 * @param integer $container_guid.
 * @param string $resource_type Tipo de archivo (question,file...).
 * @param string $action_type Acción que se realizó (viewed,created...).
 * @author Adolfo del Sel Llano y Victor Corchero Morais.
 * @version Elgg 1.10.
 * @package lrs_export_import
 * @subpackage lib
 */
function set_config_object_context($resource_guid,$container_guid,$resource_type,$action_type){

    global $object,$group;
    $object = get_entity($resource_guid);
    $group  = get_entity($container_guid);
    if(strcmp("removed",strtolower($action_type))==0 || get_entity($resource_guid) instanceof ElggObject || strcmp("logged",strtolower($action_type))==0){
        if(strcmp("removed",strtolower($action_type))!=0){
                set_config_object_context_other($resource_type,$resource_guid);
        }else{
                set_config_object_context_remove($resource_type,$resource_guid);       
        } 
    }else{
        set_config_object_context_event_no_exists($resource_type,$resource_guid);
    }       
    set_object_($resource_type);
    set_context_(); 
}
/**
 * Configura las opciones de las variables $name, $id y $parent.
 * 
 * Esta función configura las variables $name, $id y $parent en función del subtipo de la acción.
 * Esta es usada cuando se ha creado un evento y se ha borrado antes de que el cron sea ejecutado. 
 * Quedará reflejado en la base de datos con un "[NO_EXISTS...]" lo que 
 * significa que en el momento de envio el evento ya no existía en la DB de Elgg.
 *  
 * 
 * @global string Variable de la estructura json que especifica los valores del grupo al cual perteneces.
 * @global integer Identificador del objeto.
 * @global string Nombre del objeto.
 * @global string Indica si el objeto en cuestión esta supeditado a otro.
 * @param integer $resource_guid Identificador del recurso.
 * @param string $resource_type Tipo de archivo (question,file...).
 * @author Adolfo del Sel Llano y Victor Corchero Morais.
 * @version Elgg 1.10.
 * @package lrs_export_import
 * @subpackage lib
 */
function set_config_object_context_event_no_exists($resource_type,$resource_guid){

    global $group,$id,$name,$parent;
    switch ($resource_type) {
        case 'comment':
                       
                            $name   = "[NO_EXISTS_COMMENT] ".$resource_guid;
                            $id     = elgg_get_site_url();
                            if($group instanceof ElggObject){
                                $parent = array(
                                                'contextActivities' =>array(
                                                                            'parent' => array(
                                                                                                'objectType' => 'Activity',
                                                                                                'id'         => $group->getURL(),
                                                                                        )
                                                                        )
                                            );
                            }
                            $aux    = $group->container_guid;
                            $group  = get_entity($aux);
                        
                        break;
        case 'thewire':
                     
                            $name   = "[NO_EXISTS_WIRE] ".$resource_guid;
                            $id     = elgg_get_site_url();
                        
                        break;
        case 'discussion_reply':
        case 'responsed':
        case 'answer':
                       
                            $name   = "[NO_EXISTS_ANSWER] ".$resource_guid;
                            $id     = elgg_get_site_url();
                            if($group instanceof ElggObject){
                                $parent = array(
                                                'contextActivities' =>array(
                                                                            'parent' => array(
                                                                                                'objectType' => 'Activity',
                                                                                                'id' => $group->getURL(),
                                                                                        )
                                                                        )
                                            );
                            }
                        break;
        default:
                            $name = "[NO_EXISTS] ".$resource_guid;
                            $id   = elgg_get_site_url();                
                        break;
    }
}
/**
 * Configura las opciones de las variables $name, $id y $parent.
 * 
 * Esta función configura las variables $name, $id y $parent en función del subtipo de la acción.
 * Esta es usada cuando se ha creado un evento y no se ha borrado antes de que el cron sea ejecutado.
 *  
 * 
 * @global string Variable de la estructura json que especifica los valores del objeto.
 * @global string Variable de la estructura json que especifica los valores del grupo al cual perteneces.
 * @global integer Identificador del objeto.
 * @global string Nombre del objeto.
 * @global string Indica si el objeto en cuestión esta supeditado a otro.
 * @param integer $resource_guid Identificador del recurso
 * @param string $resource_type Tipo de archivo (question,file...)
 * @author Adolfo del Sel Llano y Victor Corchero Morais.
 * @version Elgg 1.10.
 * @package lrs_export_import
 * @subpackage lib
 */
function set_config_object_context_other($resource_type,$resource_guid){
    
    global $object,$group,$id,$name,$parent;
    switch ($resource_type) {
        case 'comment': 
                            $name   = "[COMMENT] ". $resource_guid;
                            $id     = $object->getURL();
                            $parent = array(
                                            'contextActivities' =>array(
                                                                        'parent' => array(
                                                                                            'objectType' => 'Activity',
                                                                                            'id'         => $group->getURL(),
                                                                                    )
                                                                    )
                                        );
                            $aux    = $group->container_guid;
                            $group  = get_entity($aux);
                        break;
        case 'thewire':

                            $name   = "[WIRE] ".$resource_guid;
                            $id     = $object->getURL();

                        break;
        case 'discussion_reply':
        case 'responsed':
        case 'answer':

                            $name   = "[ANSWER] ".$resource_guid;
                            $id     = $object->getURL();
                            $parent = array(
                                            'contextActivities' =>array(
                                                                        'parent' => array(
                                                                                            'objectType' => 'Activity',
                                                                                            'id' => get_entity($object->container_guid)->getURL(),
                                                                                    )
                                                                    )
                                        );
                        
                        break;
        default:
                            $name = $object->getDisplayName()." (".$resource_guid.")";
                            $id   = $object->getURL();
                        break;
    }
}
/**
 * Configura las opciones de las variables $name, $id y $parent.
 * 
 * Esta función configura las variables $name, $id y $parent en función del subtipo de la acción.
 * Esta es usada cuando se ha creado un evento ,no se ha borrado antes de que el cron sea ejecutado
 * y la acción ha realizar sea "removed".
 *  
 * 
 * @global string Variable de la estructura json que especifica los valores del grupo al cual perteneces.
 * @global integer Identificador del objeto.
 * @global string Nombre del objeto.
 * @global string Indica si el objeto en cuestión esta supeditado a otro.
 * @param integer $resource_guid Identificador del recurso.
 * @param string $resource_type Tipo de archivo (question,file...).
 * @author Adolfo del Sel Llano y Victor Corchero Morais.
 * @version Elgg 1.10.
 * @package lrs_export_import
 * @subpackage lib
 */
function set_config_object_context_remove($resource_type,$resource_guid){

    global $group,$id,$name,$parent;
     switch ($resource_type) {
        case 'comment':
                       
                            $name   = "[REMOVED_COMMENT] ".$resource_guid;
                            $id     = elgg_get_site_url();
                            if($group instanceof ElggObject){
                                $parent = array(
                                                'contextActivities' =>array(
                                                                            'parent' => array(
                                                                                                'objectType' => 'Activity',
                                                                                                'id'         => $group->getURL(),
                                                                                        )
                                                                        )
                                            );
                            }
                            $aux    = $group->container_guid;
                            $group  = get_entity($aux);
                        break;
        case 'thewire':
                     
                            $name   = "[REMOVED_WIRE] ".$resource_guid;
                            $id     = elgg_get_site_url(); 
                        break;
        case 'discussion_reply':
        case 'responsed':
        case 'answer':
                       
                            $name   = "[REMOVED_ANSWER] ".$resource_guid;
                            $id     = elgg_get_site_url();
                            if($group instanceof ElggObject){
                                $parent = array(
                                                'contextActivities' =>array(
                                                                            'parent' => array(
                                                                                                'objectType' => 'Activity',
                                                                                                'id' => $group->getURL(),
                                                                                        )
                                                                        )
                                            );
                            }      
                        break;
        default:
                            $name = "[REMOVED] ".$resource_guid;
                            $id   = elgg_get_site_url();
                        break;
    }
}
/**
 * Configura las opciones de la variable $object.
 * 
 * Esta función, configura las opciones de la variable $object.
 *  
 * 
 * @global string Variable de la estructura json que especifica los valores del objeto.
 * @global string Identificador del objeto.
 * @global string Nombre del objeto.
 * @param string $resource_type Tipo de archivo (question,file...) 
 * @author Adolfo del Sel Llano y Victor Corchero Morais.
 * @version Elgg 1.10.
 * @package lrs_export_import
 * @subpackage lib
 */
function set_object_($resource_type){

    global $object,$id,$name;
    $object = array(
                    'objectType' => 'Activity',
                    'id'         => $id,
                    'definition' => array(
                                            'name' => array(
                                                            'en' => $name,
                                                        ),
                                            'type' => elgg_get_site_url() . "lrs_export_import/activities/" . $resource_type,
                                        ),
                );
}
/**
 * Configura las opciones de la variable $context.
 * 
 * Esta función, configura las opciones de la variable $context.
 * Le añade un campo más si el objeto en cuestion pertenece a un grupo. 
 *  
 * 
 * @global string Variable de la estructura json que especifica el contexto,entendido como: la plataforma en la que estas, si perteneces grupo, etc.
 * @global string Variable de la estructura json que especifica los valores del grupo al cual perteneces.
 * @global string Indica si el objeto en cuestión esta supeditado a otro.
 * @author Adolfo del Sel Llano y Victor Corchero Morais.
 * @version Elgg 1.10.
 * @package lrs_export_import
 * @subpackage lib
 */
function set_context_(){

    global $context,$group,$parent;
    $context = null;  
    if ($group instanceof ElggGroup){
        $team = array(
                        'team' => array(
                                        'objectType' => 'Group',
                                        'account'    => array(
                                                                'homePage' => elgg_get_site_url(),
                                                                'name'     => $group->name 
                                                            ),
                                    ),
                );
    }
    $platform = array(
                        'platform' => "Elgg v".elgg_get_version(true),
                     );  
    $context  = array_merge((array)$parent,(array)$team,(array)$platform);
}
/**
 * Envia los datos al LRS.
 * 
 * Comforma el statement con los datos elaborados en las funciones anteriores
 * y lo envia.
 *  
 * 
 * @global string Nombre de usuario.
 * @global string Variable de la estructura json que especifica los valores del usuario.
 * @global string Variable de la estructura json que especifica los valores del verbo.
 * @global string Variable de la estructura json que especifica el contexto,entendido como: la plataforma en la que estas, si perteneces grupo, etc.
 * @global string Variable de la estructura json que especifica los valores del objeto.
 * @global string Url de acceso.
 * @global string Nombre de usuario, el email.
 * @global string Clave de acceso.
 * @author Adolfo del Sel Llano y Victor Corchero Morais.
 * @version Elgg 1.10.
 * @package lrs_export_import
 * @subpackage lib
 */
function send(){

    global $user,$actor,$verb,$context,$object,$endpoint,$username,$password;
    if(empty($endpoint) || empty($username) || empty($password)){
        elgg_log('Error en la declaracion de las variables.' ,'ERROR');
    }else{
        try{
            $lrs = new RemoteLRS(array(
                                        'endpoint' => $endpoint,
                                        'version'  => '1.0.1',
                                        'username' => $username,
                                        'password' => $password
                                    )
                                );
            $statement = new TinCanStatement (array_filter(array(
                                                                'actor'   => $actor,
                                                                'verb'    => $verb,
                                                                'object'  => $object,
                                                                'result'  => null,
                                                                'context' => $context,
                                                            ))
                                                );
            $response  = $lrs->saveStatement($statement);
            if ($response->success) {
                elgg_log("Enviado correctamente", 'ERROR');
            } else {
                elgg_log("No se pudo enviar", 'ERROR');
            }
       
       } catch (\Exception $ex) {
            elgg_log("TinCan xAPI Fatal Error: " . $ex->getMessage(), 'ERROR');
            return false;
        }
    }
    return true;
}
/**
 * Analiza la cabecera.
 * 
 * Analiza la cabecera y comprueba que todo sea correcto, que el usuario
 * exista en la base de datos, que sus datos coindicen, extrae el json etc.
 *  
 * 
 * @param string $headers
 * @param integer $group
 * @author Adolfo del Sel Llano y Victor Corchero Morais
 * @version Elgg 1.10
 * @package lrs_export_import
 * @subpackage lib
 */
function import_tincan($headers,$group){

    $version = false; $content = false; $ur='';
    foreach ($headers as $header => $value) {
        switch($header){
            case 'Authorization':
                                                //de ser un usuario no autorizado o no enviar credenciales debe dar error 401 Unauthorized.
                                                //si todo va bien 204 No Content
                                                $codificado   = iconv_substr($value,6);           
                                                $decodificado = base64_decode($codificado);
                                                $credenciales = explode(':',$decodificado);
                                                $email        = (string)$credenciales[0];
                                                $password     = (string)$credenciales[1];
                                                
                                                $result       = DB_select(2,$email);
                                                $hash_db      = $result[0]->password_hash; 

                                                $bool         = Elgg\PasswordService::verify($password,$hash_db);
                                                if(!$bool){
                                                    header('HTTP/1.0 401 Unauthorized');
                                                    elgg_log("Error al autentuicar al usuario [import_tincan]",'ERROR');
                                                }
                                                break;

            case 'X-Experience-API-Version':
                                                //aceptar 1.0.0 1.0 1.0.1 dar error 400 mas pequeña descripción del error si no es una de ellas
                                                if(strcmp($value,'1.0.0')==0 || strcmp($value,'1.0.1')==0 || strcmp($value,'1.0')==0){
                                                    $version = true;
                                                    break;
                                                }
                                                header('HTTP/1.0 400 Bad Request');
                                                elgg_log('ERROR CON LA VERSION DE XPERIENCE API [import_tincan]','ERROR');
                                                break;
            case 'Content-Type':
                                                //de no ser application/json o de estar corrupto el Json error 400.
                                                if(strcmp($value,'application/json')==0){
                                                    $content = true;
                                                    break;
                                                }
                                                header('HTTP/1.0 400 Bad Request');
                                                elgg_log('ERROR CON EL CONTENT-TYPE [import_tincan]','ERROR');
                                                break;
            case 'Host': 
                                                $host = $value;
                                                break;                   
        }
    }
    if($bool && $content && $version){
        try{
                $json = file_get_contents('php://input');
                extract_info_json($json,$host,$group);
                header("HTTP/1.0 204 No Content");
                elgg_log('ENVIADO CORRECTAMENTE [import_tincan]','ERROR');
            }catch(Exception $e){
                                //enviar error 400 Json malformado
                                header('HTTP/1.0 400 Bad Request');
                                elgg_log('ERROR EN EL DOCUMENT JSON [import_tincan]','ERROR');
                                }
            }       
}
/**
 * Extrae la información del json y la guarda en la DB.
 * 
 * Optiene los diferentes campos del json y los guarda en la DB.
 *  
 * 
 * @param string $json
 * @param string $ip 
 * @param integer $group  
 * @author Adolfo del Sel Llano y Victor Corchero Morais
 * @version Elgg 1.10
 * @package lrs_export_import
 * @subpackage lib
 */
function extract_info_json ($json,$host_name,$group){

    $json_a=json_decode($json,true);

    $actor_ObjectType = $json_a[actor][objectType];

    $actor_name               = $json_a[actor][name];
    $actor_mbox               = $json_a[actor][mbox];

    $verb_id   = $json_a[verb][id];
    $verb_name = $json_a[verb][display][en];

    $context_contextActivities_objectType = $json_a[context][contextActivities][parent][0][objectType];
    $context_contextActivities_id         = $json_a[context][contextActivities][parent][0][id];
    $context_team_homePage                = $json_a[context][team][account][homePage];
    $context_team_name                    = $json_a[context][team][account][name];
    $context_team_objecType               = $json_a[context][team][objectType];
    $context_platform                     = $json_a[context][platform];

    $object_objecType = $json_a[object][objectType];
    $object_id        = $json_a[object][id];
    $object_type      = $json_a[object][definition][type];
    $object_name      = $json_a[object][definition][name][en];

    $time = $json_a[timestamp];

    $actor_mbox_array  = explode(':',$actor_mbox);
    $mbox              = (string)$actor_mbox_array[1];
    $result            = DB_select(2,$mbox);

    $platform      = $context_platform;
    $actor_guid    = $result[0]->guid;
    $action_type   = strtoupper($verb_name);
    $resource_type = array_pop(explode('/',$object_type));
   
   
    $id_parent     = $context_contextActivities_id;
    $group_name    = $context_team_name;
    if(strcmp($time,'')==0){
        $aux = time();
        $time = date ('Y-m-d H:i:s',$aux);
    }

    $dbprefix = elgg_get_config('dbprefix');
    $query = "INSERT DELAYED into {$dbprefix}lrs_import "
        . "(web, platform, actor_guid, action_type, resource_type, object_name, url_object, url_parent, group_name, group_id, time_created) "
        . "VALUES ('$host_name','$platform ','$actor_guid','$action_type','$resource_type','$object_name','$object_id','$id_parent','$group_name','$group','$time')";
    insert_data($query);
}
/**
 * Optiene la consulta de la DB.
 * 
 * Dependiendo de la opción realiza una consulta u otra y devuelve 
 * el resultado.
 *  
 * 
 * @param string $value
 * @param integer $option  
 * @return array $result 
 * @author Adolfo del Sel Llano y Victor Corchero Morais
 * @version Elgg 1.10
 * @package lrs_export_import
 * @subpackage lib
 */
function DB_select($option,$value){

    $dbprefix = elgg_get_config('dbprefix');

    switch ($option) {
        case 0:
                $query = "SELECT DISTINCT id from {$dbprefix}events_log where 1 and id>$value";
                break;
        case 1:
                $query = "SELECT * from {$dbprefix}events_log where 1 and id=$value";
                break;
        case 2:
                $query = "SELECT * from {$dbprefix}users_entity where 1 and email='$value'";
                break;
        case 3:
                $query = "SELECT id,time_created from {$dbprefix}lrs_import where 1 Order By id ASC LIMIT 0,1 ";
                break;
        case 4:
                $query = "SELECT id from {$dbprefix}lrs_import where 1 and time_created<$value Order By id DESC LIMIT 0,1 ";
                break; 
        case 5:
                $query = "SELECT id from {$dbprefix}lrs_import where 1 and time_created<$value";
                break;          
        case 6:
                $query = "SELECT id from {$dbprefix}lrs_import where 1 Order By id DESC LIMIT 0,1 ";
                break;
        case 7:
                $query = "DELETE FROM {$dbprefix}lrs_import where 1 and id=$value"; 
                get_data($query);
                return 1;
                break;
        case 8:
                $query = "SELECT id from {$dbprefix}events_log where 1 Order By id DESC LIMIT 0,1 ";   
                break;                                    
    } 
    $result = get_data($query);
    return $result;
}
/**
 * Borra entradas de la tabla "elgg_lrs_imoprt".
 * 
 * Obtiene el tiempo del primer valor de la tabla , le suma el configurado
 * por el usuario y borra todas las entradas anteriores a ese tiempo.
 * Cabe destacar que si en esa seleccion de entradas anteriores selecciona
 * el ultimo valor de la tabla no se borrara ninguna entrada.
 * La razon de dicha restriccion es no dejar la tabla vacia.
 *  
 * 
 * @param string $time_of_delete Indica el tiempo de borrado. 
 * @return boolean  
 * @author Adolfo del Sel Llano y Victor Corchero Morais
 * @version Elgg 1.10
 * @package lrs_export_import
 * @subpackage lib
 */
function lrs_export_import_delete_log($time_of_delete) {

    $result = DB_select(3,0);
    $cutoff = $result[0]->time_created + $time_of_delete;

    $result = DB_select(4,$cutoff);
    $ultimo = $result[0]->id;

    $result       = DB_select(6,0);
    $ultimo_tabla = $result[0]->id;

    
    if($ultimo != $ultimo_tabla){
        $result = DB_select(5,$cutoff);
        foreach ($result as $hola) { 
             DB_select(7,$hola->id);
        }
        return true;
    }else{
        return false;
    } 
}