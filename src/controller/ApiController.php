<?php

class ApiController extends Controller {

	//An error message if appropriate
	private $error = null;

	//URL pair
	private $sourceUrl;
	private $targetUrl;

	//ID of inserted item
	private $id;

	//Determines if output is prepared for the renderer
	private $readyRender = false;

	//Batch of items if returning multiple
	private $items;

	/**
	* Return (via Ajax) json response
	*/
	public function render() {
		if($this->readyRender || $this->error){
			echo json_encode(array(
				'error' => $this->error !== null ? true : false,
				'sourceUrl' => $this->sourceUrl,
				'targetUrl' => $this->targetUrl,
				'id' => $this->id,
				'reason' => $this->error,
				'items' => $this->items
			));
		} else {
			header('Location: ?controller=PageNotFound');
		}
	}

	/**
	* Called via Ajax when client submits shorten request
	**/
	protected function submitRequest() {
		if(isset($_POST['sourceUrl']) && isset($_POST['targetUrl'])) {

			$this->sourceUrl = strtolower(trim($_POST['sourceUrl']));
			$this->targetUrl = strtolower(trim($_POST['targetUrl']));

			if (!preg_match('/^([\x00-\x7F])*$/', $this->sourceUrl)) {
				$this->error = 'This URL shortner does not support special characters in URLs.';
			} else if (!preg_match('/^([\x00-\x7F])*$/', $this->targetUrl)) {
				$this->error = 'Custom URLs cannot use special characters.';
			} else if (!filter_var($this->sourceUrl, FILTER_VALIDATE_URL)) {
				$this->error = 'Invalid link, please ensure the link is valid.';
			} else if(!empty($this->targetUrl) && (strlen($this->targetUrl) < 4 || strlen($this->targetUrl) > 20)) {
				$this->error = 'Custom URLs must be between 4 and 20 characters.';
			} else {
				$db = new Database();
				if (!empty($this->targetUrl)) {
					if ($db->resolve($this->targetUrl)) {
						$this->error = 'That custom URL has already been used';
					}
				} else {
					$this->targetUrl = $db->newRandomTarget();
				}
			}

			if (!$this->error) {
				$this->id = $db->shorten($this->sourceUrl, $this->targetUrl);
				$this->readyRender = true;
			}
		} else {
			$this->error = 'Invalid request';
		}
	}

	/**
	* Called via Ajax when page loads to get previous URL pairs
	**/
	protected function getPrevious() {
		$db = new Database();
		$this->items = $db->getLinksFromSession(session_id());
		$this->readyRender = true;
	}

	/**
	* Deletes pair by ID
	**/
	protected function deletePair() {
		$db = new Database();
		if (!$db->deletePair(intval($_GET['id']), session_id())) {
			$this->error = 'Unknown pair';
		}
		$this->readyRender = true;
	}
}
