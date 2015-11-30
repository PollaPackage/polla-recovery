<?php

namespace Rentalhost\PollaRecovery\Test;

use Rentalhost\PollaRecovery\RecoveryAccess;

/**
 * Class RecoveryAccessTest
 * @package Rentalhost\PollaRecovery\Test
 */
class RecoveryAccessTest extends Base
{
    /**
     * Test public properties.
     * @coversNothing
     */
    public function testPublicProperties()
    {
        static::assertClassHasAttribute('hash', RecoveryAccess::class);
        static::assertClassHasAttribute('password', RecoveryAccess::class);
        static::assertClassHasAttribute('timestamp', RecoveryAccess::class);
        static::assertClassHasAttribute('token', RecoveryAccess::class);
    }

    /**
     * Test __construct method.
     *
     * @covers Rentalhost\PollaRecovery\RecoveryAccess::__construct
     */
    public function testConstruct()
    {
        $recoveryAccess = new RecoveryAccess;

        static::assertNull($recoveryAccess->hash);
        static::assertNotNull($recoveryAccess->password);
        static::assertNotNull($recoveryAccess->token);
        static::assertNotNull($recoveryAccess->timestamp);

        $recoveryAccess = new RecoveryAccess(123, 456, 789);

        static::assertNull($recoveryAccess->hash);
        static::assertSame('123', $recoveryAccess->password);
        static::assertSame('456', $recoveryAccess->token);
        static::assertSame(789, $recoveryAccess->timestamp);
    }

    /**
     * Test generate method.
     *
     * @covers Rentalhost\PollaRecovery\RecoveryAccess::generate
     */
    public function testGenerate()
    {
        $recoveryAccess = RecoveryAccess::generate();

        static::assertSame(12, strlen($recoveryAccess->password));

        $recoveryAccess = RecoveryAccess::generate(123456);

        static::assertSame('123456', $recoveryAccess->password);
    }

    /**
     * Test getHash method.
     *
     * @covers Rentalhost\PollaRecovery\RecoveryAccess::getHash
     */
    public function testGetHash()
    {
        $recoveryAccess = RecoveryAccess::generate('');

        static::assertNull($recoveryAccess->getHash());

        $recoveryAccess = RecoveryAccess::generate();

        static::assertNotNull($recoveryAccess->getHash());
    }
}
