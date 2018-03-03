<?php
	require( __DIR__ . '/../vendor/autoload.php' );

	use Khalyomede\Fetch;

	$fetch = new Fetch( __DIR__ . '/config' );

	$remove_dashes = function($data) {
		return str_replace('-', '', $data);
	};

	$charset = $fetch->across($remove_dashes)->from('app.charset');

	print_r($charset);
?>