<?php

namespace App\Entity\Catalogue;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="article_type", type="string")
 * @ORM\DiscriminatorMap({"article" = "Article", "livre" = "Livre", "musique" = "Musique"})
 */
class Article
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string")
     */
    private $titre;


    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float")
     */
    private $prix;

    /**
     * @var int
     *
     * @ORM\Column(name="disponibilite", type="integer")
     */
    private $disponibilite;
	
    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string")
     */
    private $image;

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Article
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Article
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set prix
     *
     * @param float $prix
     *
     * @return Article
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return float
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set disponibilite
     *
     * @param integer $disponibilite
     *
     * @return Article
     */
    public function setDisponibilite($disponibilite)
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    /**
     * Get disponibilite
     *
     * @return int
     */
    public function getDisponibilite()
    {
        return $this->disponibilite;
    }
	

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Article
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="info_comp", type="string", nullable=true)
     */
    private $infoComp;

    /**
     * @var string
     *
     * @ORM\Column(name="info_prod", type="string", nullable=true)
     */
    private $infoProduct;

     /**
     * Set infoComp
     *
     * @param string $infoComp
     *
     * @return Article
     */
    public function setInfoComp($infoComp)
    {
        $this->infoComp = $infoComp;

        return $this;
    }

    /**
     * Get infoComp
     *
     * @return string
     */
    public function getInfoComp()
    {
        return $this->infoComp;
    }

/**
 * Set infoProduct
 *
 * @param string $infoProduct
 *
 * @return Article
 */
public function setInfoProduct($infoProduct)
{
    $this->infoProduct = $infoProduct;

    return $this;
}


/**
 * Get infoProduct
 *
 * @return string
 */
public function getInfoProduct()
{
    return $this->infoProduct;
}

}

