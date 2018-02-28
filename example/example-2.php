<?php
	require( __DIR__ . '/../vendor/autoload.php' );

	use Khalyomede\Fetch;

	$fetch = new Fetch( __DIR__ . '/config' );

	$charset = $fetch->from('database.database.host');

	print_r($charset);
?>