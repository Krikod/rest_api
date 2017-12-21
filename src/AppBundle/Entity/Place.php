<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\RestBundle\Controller\Annotations as Rest;

# On ajoute une contrainte d'unicité sur le champ name
/**
 * Place
 *
 * @ORM\Table(name="place",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="places_name_unique",columns={"name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaceRepository")
 */
class Place
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    protected $address;

    /**
     * @ORM\OneToMany(targetEntity="Price", mappedBy="place")
     * @var Price[]
     */
    protected $prices;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Theme",
     *     mappedBy="place")
     * @var Theme[]
     */
    protected $themes;

// todo !!! Nous pouvons maintenant créer un lieu tout en rajoutant des prix et le principe peut même être élargi
// pour les THEMES des lieux et les PREFERENCES des utilisateurs.
// Cela dépend peu de REST mais de la gestion des formulaires Symfony.
    public function __construct()
    {
        $this->prices = new ArrayCollection();
        $this->themes = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Place
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Place
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set prices
     *
     * @param $prices
     * @return $this
     */
    public function setPrices($prices)
    {
        $this->prices = $prices;
        return $this;
    }

    /**
     * Get prices
     *
     * @return Price[]|ArrayCollection
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Set themes
     * @param $themes
     * @return $this
     */
    public function setThemes($themes)
    {
        $this->themes = $themes;
        return $this;
    }

    /**
     * Get themes
     * @return Theme[]|ArrayCollection
     */
    public function getThemes()
    {
        return $this->themes;
    }
}
