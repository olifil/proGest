<?php

namespace proGestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Variation
 *
 * @ORM\Table(name="variation")
 * @ORM\Entity(repositoryClass="proGestBundle\Repository\VariationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Variation
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="quantite", type="integer")
     */
    private $quantite;

    /**
     * @var string
     *
     * @ORM\Column(name="prixVente", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $prixVente;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="proGestBundle\Entity\Article", inversedBy="variations")
     */
    private $article;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="proGestBundle\Entity\Vente", inversedBy="variations")
     * @ORM\JoinColumn(nullable=true)
     */
    private $vente;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isActive", type="boolean", nullable=true, options={"default": true})
     */
    private $isActive;


    public function __construct()
    {
      $this -> date = new \Datetime();
      $this -> isActive = true;
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
     * Set date
     * @ORM\PrePersist()
     *
     * @param \DateTime $date
     * @return Variation
     */
    public function setDate()
    {
        $this->date = new \DateTime();

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
     * Set quantite
     *
     * @param integer $quantite
     * @return Variation
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return integer
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set article
     *
     * @param \proGestBundle\Entity\Article $article
     * @return Variation
     */
    public function setArticle(\proGestBundle\Entity\Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \proGestBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set vente
     *
     * @param \proGestBundle\Entity\Vente $vente
     * @return Variation
     */
    public function setVente(\proGestBundle\Entity\Vente $vente = null)
    {
        $this->vente = $vente;

        return $this;
    }

    /**
     * Get vente
     *
     * @return \proGestBundle\Entity\Vente
     */
    public function getVente()
    {
        return $this->vente;
    }

    /**
     * Set prixVente
     *
     * @param string $prixVente
     * @return Variation
     */
    public function setPrixVente($prixVente)
    {
        $this->prixVente = $prixVente;

        return $this;
    }

    /**
     * Get prixVente
     *
     * @return string
     */
    public function getPrixVente()
    {
        return $this->prixVente;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Variation
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
