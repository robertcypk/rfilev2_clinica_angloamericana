<?php
namespace App\Controller;

use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

class Encoder
{
    //password generator
    public function encode($pwd)
    {
        $encoder = new BCryptPasswordEncoder(13);
        $hash = $encoder->encodePassword($pwd, '');
        return $hash;
    }
    public function matchpwd($sendpwd, $dbpwd)
    {
        $bencoder = new BCryptPasswordEncoder(13);
        $isPasswordValid = $bencoder->isPasswordValid($dbpwd, $sendpwd, '');
        return $isPasswordValid;
    }
}
