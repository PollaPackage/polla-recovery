<?php

namespace Rentalhost\PollaRecovery;

use Rentalhost\VanillaResult\Result;

/**
 * Class RecoveryChecker
 * @package Rentalhost\PollaRecovery
 */
class RecoveryCheck
{
    /**
     * Recovery validity in hours.
     * By default 168 hours (7 days).
     * @var int
     */
    private $validity;

    /**
     * The original password to accept.
     * @var string|null
     */
    private $originalPassword;

    /**
     * Check if your recovery access can match with expected recovery access.
     *
     * @param RecoveryAccess $yourRecoveryAccess     Your recovery access.
     * @param RecoveryAccess $expectedRecoveryAccess Expected recovery access.
     *
     * @return Result
     */
    public function check($yourRecoveryAccess, $expectedRecoveryAccess)
    {
        // Check token.
        if ($yourRecoveryAccess->token !== $expectedRecoveryAccess->token) {
            return new Result(false, 'token.invalid', [
                'received' => $yourRecoveryAccess->token,
                'expected' => $expectedRecoveryAccess->token,
            ]);
        }

        // Check timestamp.
        if ($this->validity !== null) {
            $validityDelta      = $this->validity * 3600;
            $validityDifference = $yourRecoveryAccess->timestamp - ( $expectedRecoveryAccess->timestamp + $validityDelta );
            if ($validityDifference >= 0) {
                return new Result(false, 'timestamp.expired', [
                    'received'   => $yourRecoveryAccess->timestamp,
                    'expiredAt'  => $expectedRecoveryAccess->timestamp + $validityDelta,
                    'difference' => $validityDifference,
                ]);
            }
        }

        // Check if your recovery access uses the recovery password.
        if (password_verify($yourRecoveryAccess->password, $expectedRecoveryAccess->getHash())) {
            return new Result(true, 'success', [
                'recovered' => true,
            ]);
        }

        // Check if your recovery access uses the original password (when it is setted).
        if ($this->originalPassword && password_verify($yourRecoveryAccess->password, $this->originalPassword)) {
            return new Result(true, 'success', [
                'recovered' => false,
            ]);
        }

        // Mark password as incorrect.
        return new Result(false, 'password.incorrect');
    }

    /**
     * Get validity in hours.
     * @return int
     */
    public function getValidity()
    {
        return $this->validity;
    }

    /**
     * Set validity in hours.
     * Set null to allow any validity hours.
     *
     * @param int|null $validity Validity hours.
     */
    public function setValidity($validity)
    {
        $this->validity = (int) $validity;
    }

    /**
     * Returns if original password is accepted.
     * @return bool
     */
    public function isOriginalPasswordAllowed()
    {
        return (bool) $this->originalPassword;
    }

    /**
     * Set the original password as alternative.
     * Set null to disable this feature.
     *
     * @param RecoveryAccess|string|null $originalPassword The original password.
     */
    public function setOriginalPassword($originalPassword)
    {
        if ($originalPassword === null) {
            $this->originalPassword = null;

            return;
        }

        $this->originalPassword = Helper::passwordHash($originalPassword);
    }
}
