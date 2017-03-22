<?php

/**
* Controller for 404 pages
**/

class NotFoundController extends Controller {

	/**
	* Renders the 404 page
	**/
	public function render() {
		$this->title = '404 Page Not Found';
		header("HTTP/1.0 404 Not Found");
		$this->preRender();

		$page = new View('404', get_object_vars($this));
		$page->render();

		$this->postRender();
	}

}
