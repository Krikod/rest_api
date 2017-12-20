<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


class Credentials
{
    protected $login;

    protected $password;


    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }
}

