<?php

namespace jrods\lib;

class SecureSessionHandler extends SessionHandler {

	protected $key, $name, $cookie;

	public function __construct($key, $name = 'MY_SESSION', $cookie = []) {
		$this->key = $key;
		$this->name = $name;
		$this->cookie = $cookie;

		$this->cookie += [
			'lifetime' => 0,
			'path'     => ini_get('session.cookie_path'),
			'domain'   => ini_get('session.cookie_domain'),
			'secure'   => isset($_SERVER['HTTPS']),
			'httponly' => true
		];

		$this->setup();
	}

	protected function setup() {
		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 1);

		session_name($this->name);

		session_set_cookie_params(
			$this->cookie['lifetime'], $this->cookie['path'],
			$this->cookie['domain'], $this->cookie['secure'],
			$this->cookie['httponly']
		);
	}

	public function start() {
		if ( version_compare(phpversion(), '5.4.0', '>=') ) {
			if (session_status() === PHP_SESSION_ACTIVE) {
				if (session_start()) {
					return (mt_rand(0, 4) === 0) ? $this->refresh() : true; // 1/5
				}
			}
		} else {
			// I'll leave this bit in, just in-case someone decides to not listen and runs PHP v5.3 or older
			if (session_id() === '') {
				if ( session_start() ) {
					return (mt_rand(0, 4) === 0) ? $this->refresh() : true; // 1/5
				}
			}
		}

		return false;
	}

	public function forget() {
		if ( version_compare(phpversion(), '5.4.0', '>=') ) {
			if (session_status() === PHP_SESSION_ACTIVE) {
				return false;
			}
		} else {
			// Same deal in the start() method, if somebody runs on PHP v5.3 or older
			if (session_id() === '') {
				return false;
			}
		}

		$_SESSION = [];

		setcookie(
			$this->name, '', time() - 42000,
			$this->cookie['path'], $this->cookie['domain'],
			$this->cookie['secure'], $this->cookie['httponly']
		);

		return session_destroy();
	}

	public function refresh() {
		return session_regenerate_id(true);
	}

	public function read($id) {
		return mcrypt_decrypt(MCRYPT_3DES, $this->key, parent::read($id), MCRYPT_MODE_ECB);
	}

	public function write($id, $data) {
		return parent::write($id, mcrypt_encrypt(MCRYPT_3DES, $this->key, $data, MCRYPT_MODE_ECB));
	}

	public function isExpired($ttl = 30) {
		$activity = isset($_SESSION['_last_activity'])
			? $_SESSION['_last_activity']
			: false;

		if ($activity !== false && time() - $activity > $ttl * 60) {
			return true;
		}

		$_SESSION['_last_activity'] = time();

		return false;
	}

	/* Need to redefine the fingerprint to not use ip address, maybe
	public function isFingerprint() {
		$hash = md5(
			$_SERVER['HTTP_USER_AGENT'] .
			(ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0'))
		);

		if (isset($_SESSION['_fingerprint'])) {
			return $_SESSION['_fingerprint'] === $hash;
		}

		$_SESSION['_fingerprint'] = $hash;

		return true;
	}
	*/

	public function isValid($ttl = 30) {
		return ! $this->isExpired($ttl) && $this->isFingerprint();
	}

	public function get($name) {
		$parsed = explode('.', $name);

		$result = $_SESSION;

		while ($parsed) {
			$next = array_shift($parsed);

			if (isset($result[$next])) {
				$result = $result[$next];
			} else {
				return null;
			}
		}

		return $result;
	}

	public function put($name, $value) {
		$parsed = explode('.', $name);

		$session =& $_SESSION;

		while (count($parsed) > 1) {
			$next = array_shift($parsed);

			if ( ! isset($session[$next]) || ! is_array($session[$next])) {
				$session[$next] = [];
			}

			$session =& $session[$next];
		}

		$session[array_shift($parsed)] = $value;
	}	

}