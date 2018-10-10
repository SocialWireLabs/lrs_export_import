<?php
/**
 * Vista que ve el usuario en su "Editar perfil->Configure sus herramientas->LRS Export Import".
 * 
 * En esta vista se gestionan las opciones de configuración de los LRS del usuario.
 * Dichas opciones se recogerán en el archivo save.php para ser guardadas como 
 * metadatos del usuario.
 * Cabe destacar el usu de jquery para conseguir dinamismo en las opciones.
 *   
 * @author Adolfo del Sel Llano y Victor Corchero Morais
 * @version Elgg 1.10
 * @package lrs_export_import
 * @subpackage views/default/plugins/lrs_export_imoprt
 */
?>
<fieldset class="elgg-fieldset">
	<!--<legend><?php echo elgg_echo('events_collector:tincan:lrs') ?></legend>-->
<div>
	<?php
	 	$user      = elgg_get_logged_in_user_entity();
		$user_id   = $user->getGUID();
		$count     = $user->count;
	    if($count > 1) {
	   		$aux = 0;
	    	for ($i=0; $i < $count; $i++) {
		      	$metadataname      = 'opciones_subida_'.$i;
		      	$endpoint_metadata = 'lrs_endpoint_'.$i;
		      	$password_metadata = 'lrs_password_'.$i;
		      	$username_metadata = 'lrs_username_'.$i;
		      	$endpoint          = $user->$endpoint_metadata;
				$username          = $user->$username_metadata;
				$password          = $user->$password_metadata;
		      	$value             = $user->$metadataname;
    ?>
        <div class="clone">
         	<?php
				echo elgg_echo('LRS_Export_Import:title');
				echo elgg_view('input/checkboxes', array(
												        'name' => 'opciones_subida[]',
												        'value' => $value,
												        'options' => array(
																            elgg_echo('LRS_Export_Import:viewed')=>'viewed',
																            elgg_echo('LRS_Export_Import:uploaded')=>'uploaded',
																            elgg_echo('LRS_Export_Import:logged')=>'logged',
																            elgg_echo('LRS_Export_Import:responsed')=>'responsed',
																            elgg_echo('LRS_Export_Import:created')=>'created',
																            elgg_echo('LRS_Export_Import:updated')=>'updated',
																            elgg_echo('LRS_Export_Import:download')=>'download',
																            elgg_echo('LRS_Export_Import:removed')=>'removed',
																            elgg_echo('LRS_Export_Import:commented')=>'commented',
																            elgg_echo('LRS_Export_Import:liked')=>'liked',
																            elgg_echo('LRS_Export_Import:unliked')=>'unliked',
																            elgg_echo('LRS_Export_Import:followed')=>'followed',
																            elgg_echo('LRS_Export_Import:unfollowed')=>'unfollowed',
								        								),
												        'align'=> 'vertical'
	    											)
							);
         	?>
         	<br>
         	<div>
	         	<label><?php echo elgg_echo('LRS_Export_Import:lrs_endpoint') ?></label>
				<div id='hola' class="elgg-text-help"><?php echo elgg_echo('LRS_Export_Import:lrs_endpoint:help') ?></div>
	         	<?php
	         		echo elgg_view("input/text", array("name" => "lrs_endpoint[]","value" => $endpoint));
	         	?>
	     		</div>
     		<div>
				<label><?php echo elgg_echo('LRS_Export_Import:lrs_username') ?></label>
				<div class="elgg-text-help"><?php echo elgg_echo('LRS_Export_Import:lrs_username:help') ?></div>
				<?php
		         	echo elgg_view("input/text", array("name" => "lrs_username[]","value" => $username));
		        ?>
	     	</div>
		    <div>
				<label><?php echo elgg_echo('LRS_Export_Import:lrs_password') ?></label>
				<div class="elgg-text-help"><?php echo elgg_echo('LRS_Export_Import:lrs_password:help') ?></div>
				<?php
		        	echo elgg_view("input/password", array("name" => "lrs_password[]","value" => $password));
		        ?>
		    </div>
		    <br>
		    <br>
     		<?php
		        if ($i>0){	
		    ?>							
		    <a class="remove" href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><?php echo elgg_echo("delete"); ?></a>
		    <br>   
			<?php
		         }     
		    ?>
        </div>
        <?php
        	$aux++;
      		}

   			} else {
      	?>
      	<div class="clone">
	      	<?php
	      		$value = $user->opciones_subida_0;
	      		$endpoint = $user->lrs_endpoint_0;
				$username = $user->lrs_username_0;
				$password = $user->lrs_password_0;
				echo elgg_echo('LRS_Export_Import:title');
							echo elgg_view('input/checkboxes', array(
															        'name' => 'opciones_subida[]',
															        'value' => $value,
															        'options' => array(
																			            elgg_echo('LRS_Export_Import:viewed')=>'viewed',
																			            elgg_echo('LRS_Export_Import:uploaded')=>'uploaded',
																			            elgg_echo('LRS_Export_Import:logged')=>'logged',
																			            elgg_echo('LRS_Export_Import:responsed')=>'responsed',
																			            elgg_echo('LRS_Export_Import:created')=>'created',
																			            elgg_echo('LRS_Export_Import:updated')=>'updated',
																			            elgg_echo('LRS_Export_Import:removed')=>'removed',
																			            elgg_echo('LRS_Export_Import:commented')=>'commented',
																			            elgg_echo('LRS_Export_Import:liked')=>'liked',
																			            elgg_echo('LRS_Export_Import:unliked')=>'unliked',
																			            elgg_echo('LRS_Export_Import:followed')=>'followed',
																			            elgg_echo('LRS_Export_Import:unfollowed')=>'unfollowed',
											        							),
																	'align'=> 'vertical'
		   														)
											);

	      	?>
	      	<br>
	        <div>
	         	<label><?php echo elgg_echo('LRS_Export_Import:lrs_endpoint') ?></label>
				<div id='hola' class="elgg-text-help"><?php echo elgg_echo('LRS_Export_Import:lrs_endpoint:help') ?></div>
	      		<?php
	      	 		echo elgg_view("input/text", array("name" => "lrs_endpoint[]","value" => (string)$endpoint));
	      	 	?>
	     	</div>
	     	<div>
				<label><?php echo elgg_echo('LRS_Export_Import:lrs_username') ?></label>
				<div class="elgg-text-help"><?php echo elgg_echo('LRS_Export_Import:lrs_username:help') ?></div>
				<?php
		        	echo elgg_view("input/text", array("name" => "lrs_username[]","value" => (string)$username));
		        ?>
		    </div>
	     	<div>
				<label><?php echo elgg_echo('LRS_Export_Import:lrs_password') ?></label>
				<div class="elgg-text-help"><?php echo elgg_echo('LRS_Export_Import:lrs_password:help') ?></div>
				<?php
		        	echo elgg_view("input/password", array("name" => "lrs_password[]","value" => (string)$password));
		      	?>     
	        </div>  
	        <br> 
        </div>
      	<?php
  			}
   		?>
<a href="#" class="add" rel=".clone"><?php echo elgg_echo("LRS_Export_Import:newLRS"); ?></a>
<br><br>
<script type="text/javascript">
// remove function for the jquery clone plugin
$(function(){
   var removeLink = '<a class="remove" href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><?php echo elgg_echo("delete");?></a>';
   $('a.add').relCopy({ append: removeLink});
});
</script>
<script type="text/javascript" src="<?php echo elgg_get_site_url(); ?>mod/lrs_export_import/lib/reCopy.js"></script><!-- copy field jquery plugin -->