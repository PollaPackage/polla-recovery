<?php

namespace Rentalhost\VanillaRecovery\Test;

use Rentalhost\VanillaRecovery\Helper;
use Rentalhost\VanillaRecovery\RecoveryAccess;

/**
 * Class HelperTestTest
 * @package Rentalhost\VanillaRecovery\Test
 */
class HelperTest extends Base
{
    /**
     * Test passwordHash method.
     *
     * @covers Rentalhost\VanillaRecovery\Helper::passwordHash
     */
    public function testPasswordHash()
    {
        $passwordPlain  = '123456';
        $passwordHash   = Helper::passwordHash($passwordPlain);
        $passwordRehash = Helper::passwordHash($passwordHash);

        static::assertTrue(password_verify($passwordPlain, $passwordHash));
        static::assertTrue(password_verify($passwordPlain, $passwordRehash));

        $recoveryAccess             = new RecoveryAccess($passwordRehash);
        $passwordFromRecoveryAccess = Helper::passwordHash($recoveryAccess);

        static::assertTrue(password_verify($passwordPlain, $passwordFromRecoveryAccess));
    }
}
