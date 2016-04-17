<?php

namespace Rentalhost\VanillaRecovery;

/**
 * Class Helper
 * @package Rentalhost\VanillaRecovery
 */
class Helper
{
    /**
     * Get the password hash if it is plaintext.
     * If hashed password is passed, so just returns it.
     *
     * @param RecoveryAccess|string $password Password to hash.
     *
     * @return string
     */
    public static function passwordHash($password)
    {
        if ($password instanceof RecoveryAccess) {
            $password = $password->password;
        }

        // Check password status.
        // It should detect if is a hashed password.
        $passwordStatus = password_get_info($password);
        if ($passwordStatus['algo'] === 0) {
            $hashOptions = [ ];

            // Overwrite the default cost.
            if (defined('VANILLA_RECOVERY_HASH_COST')) {
                $hashOptions['cost'] = VANILLA_RECOVERY_HASH_COST;
            }

            return (string) password_hash($password, PASSWORD_BCRYPT, $hashOptions);
        }

        return $password;
    }
}
