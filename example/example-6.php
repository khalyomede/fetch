<?php
	require( __DIR__ . '/../vendor/autoload.php' );

	use Khalyomede\Fetch;

	$fetch = new Fetch( __DIR__ . '/config' );

	$upper = function($data) {
		return strtoupper($data);
	};

	$charset = $fetch->across($upper)->from('app.charset');

	print_r($charset);
?>