<?php

namespace proGestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Entity\Variarion;

/**
 * Article
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="proGestBundle\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=16, nullable=true)
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=100)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="descriptif", type="text", nullable=true)
     */
    private $descriptif;

    /**
     * @var string
     *
     * @ORM\Column(name="prix_achat", type="decimal", precision=6, scale=2)
     */
    private $prixAchat;

    /**
    * @ORM\ManyToOne(targetEntity="proGestBundle\Entity\Fournisseur", inversedBy="articles")
    * @ORM\JoinColumn(nullable=false)
    */
    private $fournisseur;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="livre", type="integer", nullable=true, options={"default": 0})
     */
    private $livre;

    /**
     * @var integer
     *
     * @ORM\Column(name="vendu", type="integer", nullable=true, options={"default": 0})
     */
    private $vendu;

    /**
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer", nullable=true, options={"default": 0})
     */
    private $stock;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isActive", type="boolean", nullable=true, options={"default": true})
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity="proGestBundle\Entity\Variation", mappedBy="article")
     */
    private $variations;

    /**
     * @ORM\OneToMany(targetEntity="proGestBundle\Entity\PrixMultiple", mappedBy="article")
     */
    private $prixmultiples;

    private $chiffre_affaire;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->date = new \Datetime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Article
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set descriptif
     *
     * @param string $descriptif
     *
     * @return Article
     */
    public function setDescriptif($descriptif)
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    /**
     * Get descriptif
     *
     * @return string
     */
    public function getDescriptif()
    {
        return $this->descriptif;
    }

    /**
     * Set prixAchat
     *
     * @param string $prixAchat
     *
     * @return Article
     */
    public function setPrixAchat($prixAchat)
    {
        $this->prixAchat = $prixAchat;

        return $this;
    }

    /**
     * Get prixAchat
     *
     * @return string
     */
    public function getPrixAchat()
    {
        return $this->prixAchat;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Article
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set fournisseur
     *
     * @param \proGestBundle\Entity\Fournisseur $fournisseur
     *
     * @return Article
     */
    public function setFournisseur(\proGestBundle\Entity\Fournisseur $fournisseur)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return \Geograph\CodetBundle\Entity\Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    public function getLivre()
    {
        return $this->livre;
    }
    public function setLivre($livre)
    {
        $this->livre = $livre;
    }

    public function getVendu()
    {
        return $this->vendu;
    }

    public function setVendu($vendu)
    {
        $this->vendu = $vendu;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Article
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    public function getChiffreAffaire()
    {
        return $this->chiffre_affaire;
    }

    public function setChiffreAffaire($chiffre_affaire)
    {
        $this->chiffre_affaire = $chiffre_affaire;
    }

    /**
     * Add variations
     *
     * @param \proGestBundle\Entity\Variation $variations
     * @return Article
     */
    public function addVariation(\proGestBundle\Entity\Variation $variations)
    {
        $this->variations[] = $variations;

        return $this;
    }

    /**
     * Remove variations
     *
     * @param \proGestBundle\Entity\Variation $variations
     */
    public function removeVariation(\proGestBundle\Entity\Variation $variations)
    {
        $this->variations->removeElement($variations);
    }

    /**
     * Get variations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVariations()
    {
        return $this->variations;
    }

    /**
     * Add prixmultiples
     *
     * @param \proGestBundle\Entity\PrixMultiple $prixmultiples
     * @return Article
     */
    public function addPrixmultiple(\proGestBundle\Entity\PrixMultiple $prixmultiples)
    {
        $this->prixmultiples[] = $prixmultiples;

        return $this;
    }

    /**
     * Remove prixmultiples
     *
     * @param \proGestBundle\Entity\PrixMultiple $prixmultiples
     */
    public function removePrixmultiple(\proGestBundle\Entity\PrixMultiple $prixmultiples)
    {
        $this->prixmultiples->removeElement($prixmultiples);
    }

    /**
     * Get prixmultiples
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrixmultiples()
    {
        return $this->prixmultiples;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Article
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
