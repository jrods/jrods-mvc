<?php

namespace jrods\lib;

class User {

	const HASH = PASSWORD_DEFAULT;
	const COST = 14;

	private $db;

	private $data;

	function __construct($db, $session = []) {
		$this->db = $db;

		$this->data = new \stdClass();

		if(isset($session['username'], $session['password'])) {
			$this->data->username   = $session['username'];
			$this->data->input_pass = $session['password'];
		} else {
			$this->data->username   = '';
			$this->data->input_pass = '';
		}

		$_POST['username'] = '';
		$_POST['password'] = '';

	}

	public function login() {
		if ( password_verify($password, $this->data->pass_hash) ) {
			
			if ( password_needs_rehash($this->data->pass_hash, self::HASH, ['cost' => self::COST]) ) {
				$this->updatePassword($password);
				$this->save();
			}

			return true;
		}

		return false;
	}

	public function addUser($username, $password) {

	}

	private function getUser($username) {
		$sql = "select username, pass_hash from users where username = :username";
		$query = $this->db->prepare($sql);
		$parameters = [':username', $username];
		$query->execute($parameters);

		return $query->fetch();
	}

	private function updatePassword($password) {
		$this->data->pass_hash = password_hash($password, self::HASH, ['cost' => self::COST]);
	}

	private function save() {

	}

}