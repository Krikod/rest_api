<?php
# src/AppBundle/Security/AuthTokenUserProvider.php

// Comme pour tous les systèmes d’authent. de Sf, on a besoin d’un fournisseur d’utilisateurs
// (UserProvider). Ici, il faut que le fournisseur puisse charger un token en utilisant la valeur
// dans l'entête X-Auth-Token.
// On décide qu'un token d’authentification est invalide si son ancienneté est > à 12h.

namespace AppBundle\Security;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthTokenUserProvider implements UserProviderInterface
{
    protected $authTokenRepository;
    protected $userRepository;

    public function __construct(EntityRepository $authTokenRepository, EntityRepository $userRepository)
    {
        $this->authTokenRepository = $authTokenRepository;
        $this->userRepository = $userRepository;
    }

    public function getAuthToken($authTokenHeader)
    {
        return $this->authTokenRepository->findOneByValue($authTokenHeader);
    }

    public function loadUserByUsername($email)
    {
        return $this->userRepository->findByEmail($email);
    }

    public function refreshUser(UserInterface $user)
    { // Le système d'authentification est stateless,
        // on ne doit donc jamais appeler la méthode refreshUser
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    { // Cette classe permettra de récupérer les utilisateurs
        // en se basant sur le token d’authentification fourni.
        return 'AppBundle\Entity\User' === $class;
    }
// Pour piloter le mécanisme d’authentification:
//=> créer une classe implémentant l’interface ***SimplePreAuthenticatorInterface*** de Sf
//=> cette classe gère la cinématique d’authentification décrite plus haut.
}