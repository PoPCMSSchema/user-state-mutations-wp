<?php

declare(strict_types=1);

namespace PoPSchema\UserStateMutationsWP\TypeAPIs;

use PoPSchema\UserStateMutations\TypeAPIs\UserStateTypeAPIInterface;
use PoP\ComponentModel\Misc\GeneralUtils;

/**
 * Methods to interact with the Type, to be implemented by the underlying CMS
 */
class UserStateTypeAPI implements UserStateTypeAPIInterface
{
    /**
     * @return mixed Result or Error
     */
    public function login(array $credentials)
    {
        // Convert params
        if (isset($credentials['login'])) {
            $credentials['user_login'] = $credentials['login'];
            unset($credentials['login']);
        }
        if (isset($credentials['password'])) {
            $credentials['user_password'] = $credentials['password'];
            unset($credentials['password']);
        }
        if (isset($credentials['remember'])) {
            // Same param name, so do nothing
        }
        $result = \wp_signon($credentials);

        // Set the current user already, so that it already says "user logged in" for the toplevel feedback
        if (!GeneralUtils::isError($result)) {
            $user = $result;
            \wp_set_current_user($user->ID);
        }

        return \PoP\Application\Utils::returnResultOrConvertError($result);
    }

    public function logout(): void
    {
        \wp_logout();

        // Delete the current user, so that it already says "user not logged in" for the toplevel feedback
        global $current_user;
        $current_user = null;
        \wp_set_current_user(0);
    }
}
