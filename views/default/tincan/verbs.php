<?php

namespace Elgg\TinCan;

$verb = get_input('verb');

echo json_encode(array(
	'name' => array(
		'en' => elgg_echo("tincan:verb:$verb"),
	),
	'description' => array(
		'en' => elgg_echo("tincan:verb:$verb:desc", array(), 'en')
	)
));
