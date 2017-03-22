<?php

/**
* Controller for the homepage
**/

class HomeController extends Controller {

	/**
	* Renders the homepage
	**/
	public function render() {
		$this->title = 'Home';

		$this->preRender();

		$page = new View('home', get_object_vars($this));
		$page->render();

		$this->postRender();
	}
}
