<?php

class Database {

	private $connection;

	/**
	* Make database connection.
	*/
	function __construct() {
		require_once('conf/db-conf.php');
		try {
    		$this->connection = new PDO('mysql:host=' . MYSQL_HOSTNAME . ';dbname=' . MYSQL_DATABASE, MYSQL_USERNAME, MYSQL_PASSWORD);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			error_log($e);
			die();
		}
	}

	/**
	* Returns the full URL if a given targetUrl sourceUrl pair exists
	*/
	public function resolve($targetUrl) {
		$statement = $this->connection->prepare('SELECT sourceUrl FROM links WHERE targetUrl = :targetUrl');
		$statement->bindParam(':targetUrl', $targetUrl, PDO::PARAM_STR);
		$statement->execute();
		$result = $statement->fetchAll();
		if (count($result) < 1) {
			return false;
		} else {
			$statement = $this->connection->prepare('UPDATE links SET views = views + 1 WHERE targetUrl = :targetUrl');
			$statement->bindParam(':targetUrl', $targetUrl, PDO::PARAM_STR);
			$statement->execute();
			return $result[0]['sourceUrl'];
		}
	}

	/**
	* Creates a new random target that has not been used before.
	*/
	public function newRandomTarget() {
		$unique = false;
		while (!$unique) {
			$generated = substr(md5(microtime()), rand(0, 26), 5);
			if ($this->resolve($generated) == false){
				$unique = true;
			}
		}
		return $generated;
	}


	/**
	* Saves the pair in the database
	*/
	public function shorten($sourceUrl, $targetUrl) {
		$sessionId = session_id();
		$statement = $this->connection->prepare('INSERT INTO links (sourceUrl, targetUrl, sessionId) VALUES (:sourceUrl, :targetUrl, :sessionId)');
		$statement->bindParam(':sourceUrl', $sourceUrl, PDO::PARAM_STR);
		$statement->bindParam(':targetUrl', $targetUrl, PDO::PARAM_STR);
		$statement->bindParam(':sessionId', $sessionId, PDO::PARAM_STR);
		$statement->execute();
		return $this->connection->lastInsertId();
	}

	/**
	* Returns link pairs created by user with sessionId as defined
	*/
	public function getLinksFromSession($sessionId) {
		$statement = $this->connection->prepare('SELECT id, sourceUrl, targetUrl FROM links WHERE sessionId = :sessionId ORDER BY id DESC');
		$statement->bindParam(':sessionId', $sessionId, PDO::PARAM_STR);
		$statement->execute();
		$result = $statement->fetchAll();
		return $result;
	}

	/**
	* Deletes pair of URLs by ID if session matches
	* Returns true if row deleted
	*/
	public function deletePair($id, $sessionId) {
		$statement = $this->connection->prepare('DELETE FROM links WHERE id = :id AND sessionId = :sessionId');
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->bindParam(':sessionId', $sessionId, PDO::PARAM_STR);
		$statement->execute();

		return $statement->rowCount() === 1;
	}
}
