<?php

/**
* Controller for URL requests (Simplified links)
*/

class UrlRequestController extends Controller {

	/**
	* Redirects to full link
	*/
	public function render() {
		$db = new Database();
		foreach ($_GET as $targetUrl => $blank) {
			if ($target = $db->resolve($targetUrl)) {
				header('Location: ' . $target);
			}
		}
	}

}
