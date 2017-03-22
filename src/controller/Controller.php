<?php

/**
* Controller is an abstract class.
* To be used to develop business logic for each logical component of the service.
* Quickly implements method routing, however needs improvement.
**/
abstract class Controller {

	//Page title
	protected $title;

	/**
	* Handles method routing.
	**/
	function __construct() {
		if(isset($_GET['method'])){
			if(method_exists($this, $_GET['method'])){
				$method = $_GET['method'];
				$this->$method();
			} else {
				header('?controller=PageNotFound');
			}
		}
	}

	/**
	* To be called before a HTML page is rendered, contains the header
	**/
	protected function preRender() {
		$pre = new View('header', get_object_vars($this));
		$pre->render();
	}

	/**
	* To be called before a HTML page is rendered, contains the footer
	**/
	protected function postRender() {
		$post = new View('footer', get_object_vars($this));
		$post->render();
	}

	/**
	* To be called when ready to write HTML to screen
	**/
	public abstract function render();

}
