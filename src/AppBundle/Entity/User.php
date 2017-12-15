<?php

namespace AppBundle\Entity;

use AppBundle\Form\ThemeType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\User\UserInterface;
// Ajouter ***implements UserInterface*** à la classe User (après l'ajout de l'encoder dans security.yml)
/**
 * User
 *
 * @ORM\Table(name="user",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="users_email_unique",columns={"email"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    const MATCH_VALUE_THRESHOLD = 25;
    const MATCH_VALUE_BUDGET_THRESHOLD = 20;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Preference",
     *     mappedBy="user")
     * @var Preference []
     */
    protected $preferences;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Budget",
     *     mappedBy="user")
     * @var Budget
     */
    protected $budget;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    protected $plainPassword;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->preferences = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set preferences
     *
     * @param $preferences
     * @return $this
     */
    public function setPreferences($preferences)
    {
        $this->preferences = $preferences;
        return $this;
    }

    /**
     * Get preferences
     *
     * @return Preference[]|ArrayCollection
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    // Calcul du niveau de correspondance entre les thèmes d'un lieu et les préférences de l'utilisateur

    /**
     * @param ArrayCollection $themes
     * @return bool
     */
    public function preferencesMatch($themes)
    {
           $matchValue = 0;
           foreach ($this->preferences as $preference) {
               foreach ($themes as $theme) {
                   if ($preference->matchT($theme)) {
                       $matchValue += $preference->getValue() * $theme->getValue();
                   }
               }
           }

           return $matchValue >= self::MATCH_VALUE_THRESHOLD;
    }

    /**
     * @param ArrayCollection $prices
     * @return bool
     */
    public function budgetMatch($prices)
    {
        $matchValueB = 0;
        foreach ($prices as $price) {

            if ($this->budget->matchB($price)) {
                $matchValueB += $this->budget->getValue() * $price->getValue();
            }
        }
        return $matchValueB <= self::MATCH_VALUE_BUDGET_THRESHOLD;
    }

    /**
     * Set budget
     *
     * @param $budget
     * @return $this
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;
        return $this;
    }

    /**
     * Get budget
     *
     * @return Budget
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    // Ajout en raison de UserInterface

    /**
     * @return array
     */
    public function getRoles()
    {
        return [];
    }

// http://symfony.com/doc/3.3/security/entity_provider.html
// Do you need to use a Salt property?
// If you use bcrypt, no. Otherwise, yes. All passwords must be hashed with a salt, but bcrypt does this internally.
// Since this tutorial does use bcrypt, the getSalt() method in User can just return null (it's not used).
// If you use a different algorithm, you'll need to uncomment the salt lines in the User entity and add a persisted
// salt property.
    /**
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // Suppression des données sensibles
        $this->plainPassword = null;
    }
}

