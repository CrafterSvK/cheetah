<?php
declare(strict_types=1);

namespace cheetah\essentials;

/**
 * Basic token Authentification for API or non API sites.
 * @author Jakub Janek
 */
class Auth {
    public function __construct() {
        $this->mysqli = new Database();
	}
	
	public function register(string $username, string $password) {
		$password_hash = password_hash($password, PASSWORD_DEFAULT);

		$query = "INSERT INTO users (username, password) VALUES (?, ?)";
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param("ss", $username, $password_hash);
		$stmt->execute();

		return true;
	}

    /** 
     * Authentificate using username & password. Returns token to authorize.
     * May be changed.
     * @param string $username Username of the user
     * @param string $password Password of the user
     */
    public function authentificate(string $username, string $password) {
        $query = "SELECT password FROM users WHERE username=?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
		
		$stmt->bind_result($password_db);
		$stmt->fetch();

		if ($password_db == null) {
			return false;
		}

		$stmt->close();

        if (password_verify($password, $password_db)) {
			$refresh = $this->_generateToken();
			$token = $this->_generateToken();
			$expiration = time() + (24 * 60 * 60);
	
			$query = "INSERT INTO sessions (username, token, refresh, expiration) VALUES (?, ?, ?, ?)";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bind_param("sssi", $username, $token, $refresh, $expiration);

			if (!$stmt->execute()) {
				$stmt->close();
				return false;
			}

			$stmt->close();

            return array(
                "token" => $token,
                "refresh" => $refresh,
                "expiration" => $expiration
			);
        }

        return false;
    }

    /**
     * Authorize with token.
     * @param string $token token that was given by authentification
     */
    public function authorize(string $token) {
        $query = "SELECT expiration FROM sessions WHERE token=?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
		
		$stmt->bind_result($expiration);
		$stmt->fetch();
		
		$stmt->close();
		
        if (time() < $expiration && $expiration != null) {
            return true;
        }

        return false;
    }

    /**
     * Refreshes token using refresh token.
     * @param string $refresh refresh token given with token
     */
    public function refreshToken(string $refresh, string $expiration) {
        $expiration = time();
        $token = $this->_generateToken();

        return array(
            "token" => $token,
            "refresh" => $refresh,
            "expiration" => $expiration
        );
    }

    /**
     * Generates pseudo-random token.
     */
    private function _generateToken(): string {
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-_";

        $token = "";
        
        for ($i = 0; $i <= 64; $i++) {
            $random = random_int(0, strlen($characters) - 1);
            $token .= substr($characters, $random, 1);
        }

        return $token;
    }
}
