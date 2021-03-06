<?php

namespace proGestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vente
 *
 * @ORM\Table(name="vente")
 * @ORM\Entity(repositoryClass="proGestBundle\Repository\VenteRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Vente
{
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
     * @ORM\Column(name="nomClient", type="string", length=100, nullable=true)
     */
    private $nomClient;

    /**
     * @var string
     *
     * @ORM\Column(name="prenomClient", type="string", length=100, nullable=true)
     */
    private $prenomClient;

    /**
     * @var string
     *
     * @ORM\Column(name="adresseClient", type="string", length=255, nullable=true)
     */
    private $adresseClient;

    /**
     * @var string
     *
     * @ORM\Column(name="cpClient", type="string", length=5, nullable=true)
     */
    private $cpClient;

    /**
     * @var string
     *
     * @ORM\Column(name="communeClient", type="string", length=100, nullable=true)
     */
    private $communeClient;

    /**
     * @var int
     *
     * @ORM\Column(name="nbrArticles", type="integer", nullable=true)
     */
    private $nbrArticles;

    /**
     * @var float
     *
     * @ORM\Column(name="totalVente", type="float", precision=6, scale=2, nullable=true)
     */
    private $totalVente;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentMode", type="string", length=9, nullable=true)
     */
    private $paymentMode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateVente", type="datetime")
     */
    private $dateVente;

    /**
     * @var \Boolean
     *
     * @ORM\Column(name="isValidate", type="boolean", nullable=true)
     */
    private $isValidate;

    /**
     * @ORM\OneToMany(targetEntity="proGestBundle\Entity\Variation", mappedBy="vente")
     */
    private $variations;


    public function __construct()
    {
      $this->dateVente = new \DateTime();
      $this->isValidate = false;
      $this -> variations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nomClient
     *
     * @param string $nomClient
     * @return Vente
     */
    public function setNomClient($nomClient)
    {
        $this->nomClient = $nomClient;

        return $this;
    }

    /**
     * Get nomClient
     *
     * @return string
     */
    public function getNomClient()
    {
        return $this->nomClient;
    }

    /**
     * Set prenomClient
     *
     * @param string $prenomClient
     * @return Vente
     */
    public function setPrenomClient($prenomClient)
    {
        $this->prenomClient = $prenomClient;

        return $this;
    }

    /**
     * Get prenomClient
     *
     * @return string
     */
    public function getPrenomClient()
    {
        return $this->prenomClient;
    }

    /**
     * Set adresseClient
     *
     * @param string $adresseClient
     * @return Vente
     */
    public function setAdresseClient($adresseClient)
    {
        $this->adresseClient = $adresseClient;

        return $this;
    }

    /**
     * Get adresseClient
     *
     * @return string
     */
    public function getAdresseClient()
    {
        return $this->adresseClient;
    }

    /**
     * Set cpClient
     *
     * @param string $cpClient
     * @return Vente
     */
    public function setCpClient($cpClient)
    {
        $this->cpClient = $cpClient;

        return $this;
    }

    /**
     * Get cpClient
     *
     * @return string
     */
    public function getCpClient()
    {
        return $this->cpClient;
    }

    /**
     * Set communeClient
     *
     * @param string $communeClient
     * @return Vente
     */
    public function setCommuneClient($communeClient)
    {
        $this->communeClient = $communeClient;

        return $this;
    }

    /**
     * Get communeClient
     *
     * @return string
     */
    public function getCommuneClient()
    {
        return $this->communeClient;
    }

    /**
     * Set nbrArticles
     *
     * @param integer $nbrArticles
     * @return Vente
     */
    public function setNbrArticles($nbrArticles)
    {
        $this->nbrArticles = $nbrArticles;

        return $this;
    }

    /**
     * Get nbrArticles
     *
     * @return integer
     */
    public function getNbrArticles()
    {
        return $this->nbrArticles;
    }

    /**
     * Set totalVente
     *
     * @param float $totalVente
     * @return Vente
     */
    public function setTotalVente($totalVente)
    {
        $this->totalVente = $totalVente;

        return $this;
    }

    /**
     * Get totalVente
     *
     * @return float
     */
    public function getTotalVente()
    {
        return $this->totalVente;
    }

    /**
     * Set dateVente
     * @ORM\PrePersist()
     *
     * @param \DateTime $dateVente
     * @return Vente
     */
    public function setDateVente()
    {
        $this -> dateVente = new \DateTime();

        return $this;
    }

    /**
     * Get dateVente
     *
     * @return \DateTime
     */
    public function getDateVente()
    {
        return $this->dateVente;
    }

    /**
     * Add variations
     *
     * @param \proGestBundle\Entity\Variation $variations
     * @return Vente
     */
    public function addVariation(\proGestBundle\Entity\Variation $variations)
    {
        $this->variations[] = $variations;
        $variations -> setVente($this);

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
     * Set isValidate
     *
     * @param boolean $isValidate
     * @return Vente
     */
    public function setIsValidate($isValidate)
    {
        $this->isValidate = $isValidate;

        return $this;
    }

    /**
     * Get isValidate
     *
     * @return boolean
     */
    public function getIsValidate()
    {
        return $this->isValidate;
    }

    /**
     * Set paymentMode
     *
     * @param string $paymentMode
     * @return Vente
     */
    public function setPaymentMode($paymentMode)
    {
        $this->paymentMode = $paymentMode;

        return $this;
    }

    /**
     * Get paymentMode
     *
     * @return string
     */
    public function getPaymentMode()
    {
        return $this->paymentMode;
    }
}
