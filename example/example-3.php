<?php
	require( __DIR__ . '/../vendor/autoload.php' );

	use Khalyomede\Fetch;

	$fetch = new Fetch( __DIR__ . '/config' );

	$strategy = $fetch->from('database.option.cache.strategy');

	print_r($strategy);
?>