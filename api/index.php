<?php

	/**
	*	initiate database
	*/

	define("_SECURITY", true);
	set_time_limit(0);
	date_default_timezone_set("UTC");
	
	require_once 'Config/Configuration.php';
	require_once 'Include/Database.php';
	require_once 'Include/Routing.php';
	require_once 'Include/Loader.php';

	$db = Loader::loadDatabase();
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	Loader::loadRepository();  
	Loader::loadUtility();
	Loader::loadController();
	Loader::loadRouting();
	Loader::loadController();

	Loader::setJSONHeader();
	$data = Routing::route();

	
	echo json_encode($data);	
	
	