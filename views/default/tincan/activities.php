<?php

namespace Elgg\TinCan;

$activity = get_input('activity');

echo json_encode(array(
	'name' => array(
		'en' => elgg_echo("tincan:activity:object:$activity", array(), 'en')
	),
	'description' => array(
		'en' => elgg_echo("tincan:activity:object:$activity:desc", array(), 'en')
	)
));
