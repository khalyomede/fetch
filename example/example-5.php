<?php
	require( __DIR__ . '/../vendor/autoload.php' );

	use Khalyomede\Fetch;

	$fetch = new Fetch( __DIR__ . '/config' );

	$fetch->usingCache( __DIR__ . '/cache' );

	$timeout = $fetch->from('database.option.timeout');

	print_r($timeout);
?>