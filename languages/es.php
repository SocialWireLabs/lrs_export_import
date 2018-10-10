<?php

return array(
    // Config
    'LRS_Export_Import:period' => '¿Con qué frecuencia deseas enviar los registros?',
    'LRS_Export_Import:minute' => '1 minuto',
	'LRS_Export_Import:fiveminute' => '5 minutos',
	'LRS_Export_Import:fifteenmin' => '15 minutos',
	'LRS_Export_Import:halfhour' => '30 minutos',
	'LRS_Export_Import:daily' => '1 día',
	'LRS_Export_Import:weekly' => '1 semana',
	'LRS_Export_Import:monthly' => '1 mes',
	'LRS_Export_Import:yearly' => '1 año',
    'LRS_Export_Import:delete' => '¿Con qué frecuencia deseas eliminar los registros?',
    'LRS_Export_Import:never' => 'Nunca',
    'LRS_Export_Import:weekly' => '1 semana',
    'LRS_Export_Import:monthly' => '1 mes',
    'LRS_Export_Import:yearly' => '1 año',
    'LRS_Export_Import:viewed' => 'Visitas',
    'LRS_Export_Import:uploaded' => 'Subidas',
    'LRS_Export_Import:responsed' => 'Respuestas',
    'LRS_Export_Import:created' => 'Nuevos elementos',
    'LRS_Export_Import:updated' => 'Actualizaciones',
    'LRS_Export_Import:download' => 'Descargas',
    'LRS_Export_Import:removed' => 'Elementos borrados',
    'LRS_Export_Import:commented' => 'Comentarios',
    'LRS_Export_Import:liked' => 'Me gusta',
    'LRS_Export_Import:unliked' => 'Ya no me gusta',
    'LRS_Export_Import:followed' => 'Seguidos',
    'LRS_Export_Import:unfollowed' => 'Dejados de seguir',
    'LRS_Export_Import:logged' => 'Inicios de sesión',

	'LRS_Export_Import:lrs' => 'Learning Records Store (LRS)',
    'LRS_Export_Import:lrs_endpoint' => 'Punto final del LRS',
    'LRS_Export_Import:lrs_endpoint:help' => 'Punto final de TinCan de tu LRS (ej. Punto final de SCORM Cloud https://cloud.scorm.com/tc/123456789/)',
    'LRS_Export_Import:lrs_username' => 'Nombre de usuario del LRS',
    'LRS_Export_Import:lrs_username:help' => 'Nombre de usuario o ID de la aplicación usado para la autentificación en el LRS (ej. Identificador de la aplicación de SCORM Cloud)',
    'LRS_Export_Import:lrs_password' => 'Contraseña del LRS',
    'LRS_Export_Import:lrs_password:help' => 'Contraseña o llave usada para la autentificación en el LRS (ej. Clave Secreta de SCORM)',
    'LRS_Export_Import:title' => '<h2>Qué deseas exportar al lrs</h2><br>',

    'LRS_Export_Import:newLRS' => 'Añadir LRS',

    'LRS_Export_Import:lrs_explain' => "Un LRS (Learnin Record Store) es un nuevo concepto que va de la mano de Tin Can Api.",
    'LRS_Export_Import:lrs_details' => 'El futuro del e-Learning requiere un repositorio donde almacenar los eventos de aprendizaje. Los datos almacenados en un LRS pueden ser accesibles desde un LMS, herramientas de información u otros LRS.',
    'LRS_Export_Import:lrs_import' => "Para que los alumnos puedan importar información desde otras plataformas de e-Learning al LRS del grupo '%s', ".
            "debes proporcionarles la URL a los alumnos e indicarles cómo activar el envío de datos al LRS en la plataforma de e-Learning externa (normalmente en la sección de Configuración),".
            " el nombre de usuario y contraseña será el mismo que en Socialwire. Aquí tienes la URL:",
    'LRS_Export_Import:lrs_export' => "Si lo que quieres es exportar la informacion a tu LRS particular, tendrás que indicarle a los alumnos cómo hacerlo. En la seccioón de Configuración->".
            "Configure sus herramientas->LRS Export Import, cada usuario podrá configurar tantos LRS como desee, así como el tipo de eventos que se enviarán a cada uno de ellos."

);