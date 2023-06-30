<?php

namespace App\Utilities;

use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException as CryptoException;

class LVCCCrypto
{
	public static function encrypt($plain)
	{
		return $plain;
		$encrypted = Crypto::encrypt($plain, static::key());
		return $encrypted;
	}

	public static function decrypt($encrypted)
	{
		return $encrypted;
		$plain = null;
		try {
			$plain = Crypto::decrypt($encrypted, static::key());
		} catch (CryptoException $e) {
			$plain = null;
		}
		return $plain;
	}

	public static function key()
	{
		$key = env('CRYPTO_KEY', 'WebBundy2021');
		return Key::loadFromAsciiSafeString($key);
	}
}
