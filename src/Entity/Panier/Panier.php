<?php

namespace App\Entity\Panier;

use \ArrayObject;

/**
 * Panier
 */
class Panier
{
    /**
     * @var total
     */
    private $total;
    private $taxe;
    private $soustotal;
    private $reduction;
    private $sousTotalSansReduction;
    private $totalSansReduction;
    private $codePromo;
    private $display;
    private $valueCodePromo;
    private $colorCodePromo;

    /**
     * @var ArrayObject
     */
    private $lignesPanier;

    public function __construct()
    {
        $this->lignesPanier = new ArrayObject();
        $this->display="display: none";
    }

    public function getColorCodePromo() {
        return $this->colorCodePromo;
    }

    public function getValueCodePromo() {
        return $this->valueCodePromo;
    }

    public function getDisplay() {
        return $this->display;
    }

    public function setTotal()
    {
        $this->recalculer();
    }
    
    public function getTotal()
    {
        $this->recalculer();
        return $this->total;
    }

    public function setTaxe()
    {
        $this->recalculer();
    }
    
    public function getTaxe()
    {
        $this->recalculer();
        return $this->taxe;
    }

    public function setSoustotal()
    {
        $this->recalculer();
    }
    
    public function getSoustotal()
    {
        $this->recalculer();
        return $this->soustotal;
    }

    public function setCodePromo(string $codePromo)
    {
        $this->codePromo = $codePromo;
        $this->recalculer();
    }
    
    public function getCodePromo()
    {
        return $this->codePromo;
    }
    
    public function getLignesPanier() {
        return $this->lignesPanier;
    }

    public function getSousTotalSansReduction(): float
    {
        return $this->sousTotalSansReduction;
    }
    public function getTotalSansReduction(): float
    {
        return $this->totalSansReduction;
    }
    
    public function recalculer(): void
    {
        $it = $this->getLignesPanier()->getIterator();
        $this->total = 0.0;
        $this->soustotal = 0.0;
        $this->reduction = 0.0;
    
        while ($it->valid()) {
            $ligne = $it->current();
            $ligne->recalculer();
            $this->soustotal += $ligne->getPrixTotal();
            $it->next();
            $this->sousTotalSansReduction = $this->soustotal;
        }

		if ($this->codePromo === "baptiste15" || $this->codePromo === "elise15") {
			$this->reduction = $this->soustotal * (15 / 100);
			$this->soustotal -= $this->reduction;
            $this->valueCodePromo = "Votre code promo s'est appliqué";
            $this->display = "display: block;" ;
            $this->colorCodePromo = "color: green;";
		} elseif ($this->codePromo === "corbi30") {

			$this->reduction = $this->soustotal * (30 / 100);
			$this->soustotal -= $this->reduction;

            $this->display = "display: block;" ;
            $this->valueCodePromo = "Votre code promo s'est appliqué";
            $this->colorCodePromo = "color: green;";

		} else {
            $this->display = "display: block;" ;
            $this->valueCodePromo = "Ce code promo n'existe pas...";
            $this->colorCodePromo = "color: red;";
        }
		
    
        $this->total = $this->soustotal;
    
        if ($this->total < 30.0) {
            $this->taxe = 10.0;
        } else if ($this->total >= 30.0 && $this->total < 50.0) {
            $this->taxe = 5.0;
        } else {
            $this->taxe = 0.0;
        }
    
        $this->totalSansReduction = $this->soustotal + $this->taxe + $this->reduction;
        $this->total += $this->taxe;

    }

	
	
	public function ajouterLigne($inArticle) {
		$lp = $this->chercherLignePanier($inArticle) ;
		if ($lp == null) {
			$lp = new LignePanier() ;
			$lp->setArticle($inArticle) ; 
			$lp->setQuantite(1) ;
			$this->lignesPanier->append($lp) ;
		}
		else{
			$lp->setArticle($inArticle) ; 
			$lp->setQuantite($lp->getQuantite() + 1) ;
		}
		$this->recalculer() ;
	}
	
	public function chercherLignePanier($inArticle) {
		$lignePanier = null ;
		$it = $this->getLignesPanier()->getIterator();
		while ($it->valid()) {
			$ligne = $it->current();
			if ($ligne->getArticle()->getId() == $inArticle->getId())
				$lignePanier = $ligne ;
			$it->next();
		}
		return $lignePanier ;
	}
	


	public function supprimerLigne($id) {
		$existe = false ;
		$it = $this->getLignesPanier()->getIterator();
		while ($it->valid()) {
			$ligne = $it->current();
			if ($ligne->getArticle()->getId() == $id) {
				$existe = true ;
				$key = $it->key();
			}
			$it->next();
		}
		if ($existe)
			$this->getLignesPanier()->offsetUnset($key);
			
	}

}