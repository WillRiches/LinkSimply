<?php

/**
* LinkSimp.ly
*
* Author: Will Riches
* License: GPL-3.0
* Copyright: Will Riches 2017
* Version: 0.1
*/
mb_internal_encoding("UTF-8");
session_start();

/**
* Autoloader to pull required classes (MVC structure)
*/
function __autoload($class) {
	$directories = array(
		'controller',
		'model',
		'helper'
	);

	//Loop through directories with class files
	foreach($directories as $key => $dir){
		//Looking for the class file required
		$file = 'src/' . $dir . '/' . $class . '.php';
		try {
			//If found, break
			if(file_exists($file)) {
		        require_once($file);
				break;
				if(!class_exists($class)){
					throw new Exception('Class undefined: ' . $class);
				}
		    }
			//If searched through all directories throw error
			if($key >= count($directories)){
				throw new Exception('Class file not found ' . $class);
			}
		} catch (Exception $e) {
			error_log($e);
			die();
		}
	}
}

// Determine controller
$controllerName = 'Home';
if (isset($_GET['controller'])){
	$controllerName = $_GET['controller'];
	// Automatically reject anything but single words a-z
	if (!preg_match("/\A[a-z]+\z/", $controllerName)) {
		$controllerName = 'NotFound';
	}
// If not requesting a controller, query against URL database
} else if(count($_GET) === 1){
	$controllerName = 'UrlRequest';
}

// Append 'Controller' to equate to filename
$controllerClass = ucfirst($controllerName) . 'Controller';

// Ensure valid controller
if (!file_exists('src/controller/' . $controllerClass . '.php')){
	$controllerClass = 'NotFoundController';
}

//Load controller and request content
$controller = new $controllerClass;
$controller->render();
