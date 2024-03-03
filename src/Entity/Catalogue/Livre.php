<?php

namespace App\Entity\Catalogue;

use Doctrine\ORM\Mapping as ORM;

/**
 * Livre
 *
 * @ORM\Entity
 */
class Livre extends Article
{
    /**
     * @var string
     *
     * @ORM\Column(name="auteur", type="string")
     */
    private $auteur;


    /**
     * @var string
     *
     * @ORM\Column(name="date_de_parution", type="string")
     */
    private $dateDeParution;

    /**
     * Set auteur
     *
     * @param string $auteur
     *
     * @return Livre
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return string
     */
    public function getAuteur()
    {
        return $this->auteur;
    }


    /**
     * Set dateDeParution
     *
     * @param string $dateDeParution
     *
     * @return Livre
     */
    public function setDateDeParution($dateDeParution)
    {
        $this->dateDeParution = $dateDeParution;

        return $this;
    }

    /**
     * Get dateDeParution
     *
     * @return string
     */
    public function getDateDeParution()
    {
        return $this->dateDeParution;
    }
    
}

