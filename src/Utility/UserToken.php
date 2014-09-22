<?php
namespace CkTools\Utility;

use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Utility\Security;

class UserToken {

/**
 * Generates a serialized, encrypted and base64-encoded for identifying a user,
 * usually for using it in an URL
 *
 * @param User $user The user Entity
 * @param int $validForSeconds how long the token should be valid
 * @param array $additionalData Optional additional data for storage in the encrypted token
 * @return string
 */
	public function getTokenForUser(User $user, $validForSeconds = null, array $additionalData = []) {
		$tokenData = [
			'user_id' => $user->id,
			'generated' => time(),
			'validForSeconds' => $validForSeconds,
			'additionalData' => $additionalData
		];
		$tokenDataString = serialize($tokenData);
		$encrypted = Security::encrypt($tokenDataString, Configure::read('Security.cryptKey'));
		return base64_encode($encrypted);
	}

/**
 * Checks if the given token is valid, meaning it is valid by format. This method
 * does not check the validity
 *
 * @param string $token The string token
 * @return bool
 */
	public function isTokenValid($token) {
		$tokenData = $this->decryptToken($token);

		if (is_array($tokenData) && isset($tokenData['user_id']) && isset($tokenData['generated']) && isset($tokenData['validForSeconds'])) {
			return true;
		}
		return false;
	}

/**
 * Checks if the given token is expired
 *
 * @param string $token String token
 * @return bool
 * @throws InvalidArgumentException
 */
	public function isTokenExpired($token) {
		if (!$this->isTokenValid($token)) {
			throw new \InvalidArgumentException('This token is invalid');
		}
		$tokenData = $this->decryptToken($token);
		$tokenExpiration = $tokenData['generated'] + $tokenData['validForSeconds'];
		return $tokenExpiration >= time();
	}

/**
 * Returns the user id from the given token
 *
 * @param string $token The string token
 * @return string
 * @throws InvalidArgumentException
 */
	public function getUserIdFromToken($token) {
		if (!$this->isTokenValid($token)) {
			throw new \InvalidArgumentException('This token is invalid');
		}
		$tokenData = $this->decryptToken($token);
		return $tokenData['user_id'];
	}

/**
 * Tries to decode, decrypt and unserialize the given token and return the data as an 
 * array
 *
 * @param string $token The string token
 * @return array|false
 */
	public function decryptToken($token) {
		$tokenData = false;
		$encrypted = base64_decode($token);

		if ($encrypted) {
			$serialized = Security::decrypt($encrypted, Configure::read('Security.cryptKey'));
			$tokenData = unserialize($serialized);
		}
		return $tokenData;
	}
}