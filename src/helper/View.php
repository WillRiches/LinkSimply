<?php

class View {

	private $file;
	private $vars;

	/**
	* Each view requires a file to include.
	* Optionally can be passed an array of variables to process within the document
	**/
	function __construct($_file, $_vars = false) {
		$this->file = $_file;
		$this->vars = $_vars;
	}

	/**
	* Loads variables in the context of the view
	**/
	public function render() {
		if ($this->vars) {
			extract($this->vars);
		}

		$file = 'src/view/' . $this->file . '.php';

		try{
			if (!file_exists($file)) {
				throw new Exception('Cannot load view: ' . $this->file);
			} else {
				require_once('src/view/' . $this->file . '.php');
			}
		} catch (Exception $e) {
			error_log($e);
			die();
		}

	}

}
