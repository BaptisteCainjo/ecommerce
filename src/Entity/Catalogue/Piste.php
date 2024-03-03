<?php

namespace App\Entity\Catalogue;

use JsonSerializable;

use Doctrine\ORM\Mapping as ORM;

/**
 * Musique
 *
 * @ORM\Entity
 */
class Piste implements JsonSerializable
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
     * @ORM\Column(name="titre", type="string")
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="mp3", type="string")
     */
    private $mp3;
	
    /**
     * @ORM\ManyToOne(targetEntity="Musique",cascade={"persist"})
     */
    private $musique;

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Piste
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set titre
     *
     * @param integer $titre
     *
     * @return Piste
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
     * Set mp3
     *
     * @param string $mp3
     *
     * @return Piste
     */
    public function setMp3($mp3)
    {
        $this->mp3 = $mp3;

        return $this;
    }

    /**
     * Get mp3
     *
     * @return string
     */
    public function getMp3()
    {
        return $this->mp3;
    }
	

    /**
     * Set musique
     *
     * @param Musique $musique
     *
     * @return Piste
     */
    public function setMusique($musique)
    {
        $this->musique = $musique;

        return $this;
    }

    /**
     * Get musique
     *
     * @return Musique
     */
    public function getMusique()
    {
        return $this->musique;
    }


    /**
     * Specify data which should be serialized to JSON
     *
     * @return array
     */
	public function jsonSerialize()
    {
        return array(
            "titre" => $this->getTitre(),
			"mp3" => $this->getMp3()
        );
    }
}

