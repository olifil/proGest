<?php

namespace proGestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fournisseur
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="proGestBundle\Repository\FournisseurRepository")
 */
class Fournisseur
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
     * @ORM\Column(name="nom", type="string", length=100)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=100, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="raisonsociale", type="string", length=100, nullable=true)
     */
    private $raisonsociale;

    /**
     * @var string
     *
     * @ORM\Column(name="contact", type="string", length=100, nullable=true)
     */
    private $contact;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @var integer
     *
     * @ORM\Column(name="cp", type="string", length=5, nullable=true)
     */
    private $cp;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=100, nullable=true)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=14, nullable=true)
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="mob", type="string", length=14, nullable=true)
     */
    private $mob;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isSold", type="boolean", nullable=true, options={"default": false})
     */
    private $isSold = false;

    /**
     * @ORM\OneToMany(targetEntity="proGestBundle\Entity\Article", mappedBy="fournisseur")
     */
    private $articles;

    private $ca;

    private $marge;

    private $solde;

    /**
     * @var float
     *
     * @ORM\Column(name="cheque", type="float", precision=6, scale=2, nullable=true)
     */
    private $cheque;

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
     * @return Fournisseur
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
     * Set prenom
     *
     * @param string $prenom
     * @return Fournisseur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set contact
     *
     * @param string $contact
     *
     * @return Fournisseur
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Fournisseur
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set cp
     *
     * @param integer $cp
     *
     * @return Fournisseur
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return integer
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Fournisseur
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set tel
     *
     * @param string $tel
     *
     * @return Fournisseur
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Fournisseur
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
     * Constructor
     */
    public function __construct()
    {
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add articles
     *
     * @param \proGestBundle\Entity\Article $articles
     * @return Fournisseur
     */
    public function addArticle(\proGestBundle\Entity\Article $articles)
    {
        $this->articles[] = $articles;

        return $this;
    }

    /**
     * Remove articles
     *
     * @param \proGestBundle\Entity\Article $articles
     */
    public function removeArticle(\proGestBundle\Entity\Article $articles)
    {
        $this->articles->removeElement($articles);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Get ca
     *
     * @return string
     */
    public function getCa()
    {
        return $this->ca;
    }

    /**
     * Set email
     *
     * @param string $ca
     *
     * @return Fournisseur
     */
    public function setCa($ca)
    {
        $this->ca = $ca;

        return $this;
    }

    /**
     * Get marge
     *
     * @return string
     */
    public function getMarge()
    {
        return $this->marge;
    }

    /**
     * Set email
     *
     * @param string $marge
     *
     * @return Fournisseur
     */
    public function setMarge($marge)
    {
        $this->marge = $marge;

        return $this;
    }

    /**
     * Get solde
     *
     * @return string
     */
    public function getSolde()
    {
        return $this->solde;
    }

    /**
     * Set email
     *
     * @param string $solde
     *
     * @return Fournisseur
     */
    public function setSolde($solde)
    {
        $this->solde = $solde;

        return $this;
    }


    /**
     * Set cheque
     *
     * @param float $cheque
     * @return Fournisseur
     */
    public function setCheque($cheque)
    {
        $this->cheque = $cheque;

        return $this;
    }

    /**
     * Get cheque
     *
     * @return float
     */
    public function getCheque()
    {
        return $this->cheque;
    }

    /**
     * Set raisonsociale
     *
     * @param string $raisonsociale
     * @return Fournisseur
     */
    public function setRaisonsociale($raisonsociale)
    {
        $this->raisonsociale = $raisonsociale;

        return $this;
    }

    /**
     * Get raisonsociale
     *
     * @return string
     */
    public function getRaisonsociale()
    {
        return $this->raisonsociale;
    }

    /**
     * Set mob
     *
     * @param string $mob
     * @return Fournisseur
     */
    public function setMob($mob)
    {
        $this->mob = $mob;

        return $this;
    }

    /**
     * Get mob
     *
     * @return string
     */
    public function getMob()
    {
        return $this->mob;
    }

    /**
     * Set isSold
     *
     * @param string $isSold
     * @return Fournisseur
     */
    public function setIsSold($isSold)
    {
        $this->isSold = $isSold;

        return $this;
    }

    /**
     * Get isSold
     *
     * @return string
     */
    public function getIsSold()
    {
        return $this->isSold;
    }
}
