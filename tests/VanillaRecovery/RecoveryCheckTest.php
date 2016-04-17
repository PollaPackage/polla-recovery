<?php

namespace Rentalhost\VanillaRecovery\Test;

use Rentalhost\VanillaRecovery\RecoveryAccess;
use Rentalhost\VanillaRecovery\RecoveryCheck;

/**
 * Class RecoveryCheckTest
 * @package Rentalhost\VanillaRecovery\Test
 */
class RecoveryCheckTest extends Base
{
    /**
     * Data provider.
     */
    public function dataCheck()
    {
        $accessOriginal             = new RecoveryAccess('aaabbb', 'token', 0);
        $accessOriginalRehashed     = new RecoveryAccess('aaabbb', 'token', 0);
        $accessPasswordOne          = new RecoveryAccess('123456', 'token', 0);
        $accessPasswordOneRehashed  = new RecoveryAccess('123456', 'token', 0);
        $accessPasswordOnePrehashed = new RecoveryAccess(null, 'token', 0, $accessPasswordOneRehashed->getHash());
        $accessPasswordTwo          = new RecoveryAccess('abcdef', 'token', 0);

        $checkerDefault = new RecoveryCheck;
        $checkerDefault->setValidity(24);
        $checkerDefault->setOriginalPassword($accessOriginal);

        $checkerWithOriginalPlain = clone $checkerDefault;
        $checkerWithOriginalPlain->setOriginalPassword('aaabbb');

        return [
            // Test token invalid.
            [
                $checkerDefault,
                new RecoveryAccess(null, 1),
                new RecoveryAccess(null, 0),
                'token.invalid',
                [
                    'received' => '1',
                    'expected' => '0',
                ],
            ],
            // Test timestamp expired.
            [
                $checkerDefault,
                new RecoveryAccess(null, 'sameToken', 186401),
                new RecoveryAccess(null, 'sameToken', 100000),
                'timestamp.expired',
                [
                    'received'   => 186401,
                    'expiredAt'  => 186400,
                    'difference' => 1,
                ],
            ],
            // Test password incorrect.
            [
                $checkerDefault,
                $accessPasswordOne,
                $accessPasswordTwo,
                'password.incorrect',
            ],
            // Test valid recovery password.
            [
                $checkerDefault,
                $accessPasswordOne,
                $accessPasswordOne,
                'success',
                [ 'recovered' => true ],
            ],
            [
                $checkerDefault,
                $accessPasswordOneRehashed,
                $accessPasswordOne,
                'success',
                [ 'recovered' => true ],
            ],
            [
                $checkerDefault,
                $accessPasswordOneRehashed,
                $accessPasswordOnePrehashed,
                'success',
                [ 'recovered' => true ],
            ],
            // Test valid original password.
            [
                $checkerDefault,
                $accessOriginalRehashed,
                $accessPasswordOne,
                'success',
                [ 'recovered' => false ],
            ],
            [
                $checkerWithOriginalPlain,
                $accessOriginalRehashed,
                $accessPasswordOne,
                'success',
                [ 'recovered' => false ],
            ],
        ];
    }

    /**
     * Test check method.
     *
     * @param RecoveryCheck|null $recoveryChecker        The recovery checker to use.
     * @param RecoveryAccess     $yourRecoveryAccess     Your recovery access parameters.
     * @param RecoveryAccess     $expectedRecoveryAccess Expected recovery access parameters.
     * @param string             $resultMessage          Expected result message.
     * @param array|null         $resultData             Expected result data.
     *
     * @covers       Rentalhost\VanillaRecovery\RecoveryCheck::check
     * @dataProvider dataCheck
     */
    public function testCheck($recoveryChecker, $yourRecoveryAccess, $expectedRecoveryAccess, $resultMessage, $resultData = null)
    {
        $recoveryCheckerResult = $recoveryChecker->check($yourRecoveryAccess, $expectedRecoveryAccess);

        static::assertSame($resultMessage, $recoveryCheckerResult->getMessage());
        static::assertSame($resultData, $recoveryCheckerResult->getData());
    }

    /**
     * Test setOriginalPassword and isOriginalPasswordAllowed methods.
     *
     * @covers       Rentalhost\VanillaRecovery\RecoveryCheck::setOriginalPassword
     * @covers       Rentalhost\VanillaRecovery\RecoveryCheck::isOriginalPasswordAllowed
     */
    public function testOriginalPassword()
    {
        $checkerDefault = new RecoveryCheck;

        static::assertFalse($checkerDefault->isOriginalPasswordAllowed());

        $checkerDefault->setOriginalPassword('123456');

        static::assertTrue($checkerDefault->isOriginalPasswordAllowed());

        $checkerDefault->setOriginalPassword(null);

        static::assertFalse($checkerDefault->isOriginalPasswordAllowed());
    }

    /**
     * Test getValidity, testValidity methods.
     *
     * @covers Rentalhost\VanillaRecovery\RecoveryCheck::setValidity
     * @covers Rentalhost\VanillaRecovery\RecoveryCheck::getValidity
     */
    public function testValidity()
    {
        $recoveryChecker = new RecoveryCheck;

        static::assertNull($recoveryChecker->getValidity());

        $recoveryChecker->setValidity(24);

        static::assertSame(24, $recoveryChecker->getValidity());
    }
}
