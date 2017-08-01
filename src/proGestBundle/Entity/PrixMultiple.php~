<?php

namespace proGestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrixMultiple
 *
 * @ORM\Table(name="prix_multiple")
 * @ORM\Entity(repositoryClass="proGestBundle\Repository\PrixMultipleRepository")
 */
class PrixMultiple
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
     * @var int
     *
     * @ORM\Column(name="quantite", type="integer")
     */
    private $quantite;

    /**
     * @var string
     *
     * @ORM\Column(name="prix", type="decimal", precision=6, scale=2)
     */
    private $prix;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="proGestBundle\Entity\Article", inversedBy="prixmultiples")
     */
    private $article;


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
     * Set quantite
     *
     * @param integer $quantite
     * @return PrixMultiple
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
     * Set prix
     *
     * @param string $prix
     * @return PrixMultiple
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return string
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set article
     *
     * @param integer $article
     * @return PrixMultiple
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return integer
     */
    public function getArticle()
    {
        return $this->article;
    }
}
