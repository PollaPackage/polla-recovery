<?php

namespace Rentalhost\PollaRecovery;

/**
 * Class RecoveryAccess
 * @package Rentalhost\PollaRecovery
 */
class RecoveryAccess
{
    /**
     * Password hash.
     * @var string
     */
    public $hash;

    /**
     * Plaintext password.
     * @var string
     */
    public $password;

    /**
     * Access token.
     * @var string
     */
    public $token;

    /**
     * Timestamp.
     * @var int
     */
    public $timestamp;

    /**
     * RecoveryAccess constructor.
     *
     * @param string|null $password  Recovery password.
     * @param string|null $token     Recovery token.
     * @param int|null    $timestamp Recovery timestamp.
     * @param string|null $hash      Recovery pre-hashed password.
     */
    public function __construct($password = null, $token = null, $timestamp = null, $hash = null)
    {
        $this->password  = (string) $password;
        $this->token     = $token !== null ? (string) $token : md5(random_bytes(255));
        $this->timestamp = $timestamp !== null ? (int) $timestamp : time();
        $this->hash      = $hash;
    }

    /**
     * Generate a new recovery access.
     * Can receives a own expected password or will generate a random 12 characters password.
     *
     * @param string|null $password Password to use.
     *
     * @return self
     */
    public static function generate($password = null)
    {
        if ($password === null) {
            $password = random_bytes(12);
        }

        return new self($password);
    }

    /**
     * Generates and returns the hashed password.
     * Should returns null if none password is set.
     * @return string|null
     */
    public function getHash()
    {
        if ($this->password && !$this->hash) {
            $this->hash = Helper::passwordHash($this->password);
        }

        return $this->hash;
    }
}
