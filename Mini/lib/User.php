<?php

namespace Mini\lib;

class User {

	private $db;

	private $user, $pass, $level;

	function __construct($db, $sessionStuff = []) {
		$this->user  = 'anonymous';
		$this->pass  = '';
		$this->level = 0;

		$this->db = $db;
	}

	public function login($username, $password) {

	}

	public function update() {

	}

	public function newUser() {
		
	}

}