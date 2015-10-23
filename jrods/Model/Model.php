<?php

namespace jrods\Model;

class Model {
	/**
	 * The database connection
	 * @var $db
	 */
	private $db;

	/**
	 * When creating the model, the configs for database connection creation are needed
	 * @param $config
	 */
	function __construct($db) {
		$this->db =& $db;
	}

	public function getNavBar() {

	}

	public function getAllReleases() {
		$sql = "select * from releases";
		$query = $this->db->prepare($sql);
		$query->execute();
		return $query->fetchAll();
	}

	public function addRelease($post) {
		$sql = "INSERT INTO releases 
					(title, location, url, type, description, excerpt) 
				VALUES 
					(:title, :location, :url, :type, :description, :excerpt)";
		
		$query = $this->db->prepare($sql);
		
		$parameters = [
			':title'       => $post['title'],
			':location'    => $post['location'],
			':url'         => $post['url'],
			':type'        => $post['type'],
			':description' => $post['description'],
			':excerpt'     => $post['excerpt']
		];
		
		$query->execute($parameters);
	}
}
