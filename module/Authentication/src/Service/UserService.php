<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authentication\Service;

use Zend\Crypt\Password\Bcrypt;
use Authentication\Entity\User;

/**
 * Description of UserService
 *
 * @author marcio
 */
class UserService
{

    /**
     * Static function for checking hashed password (as required by Doctrine)
     *
     * @param User $user The identity object
     * @param string $passwordGiven Password provided to be verified
     * @return boolean true if the password was correct, else, returns false
     */
    public static function verifyHashedPassword(User $user, $passwordGiven)
    {
        $bcrypt = new Bcrypt([
            'cost' => 10
        ]);

        if (!$user->getUserActive()) {
            return false;
        }

        return $bcrypt->verify($passwordGiven, $user->getUserPassword());
    }

    /**
     * Encrypt Password
     *
     * Creates a Bcrypt password hash
     *
     * @return String
     */
    public static function encryptPassword($password)
    {
        $bcrypt = new Bcrypt([
            'cost' => 10
        ]);
        $encryptedPassword  = $bcrypt->create($password);

        return $encryptedPassword;
    }
}
