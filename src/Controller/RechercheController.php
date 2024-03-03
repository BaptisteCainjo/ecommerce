<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Catalogue\Livre;
use App\Entity\Catalogue\Musique;
use App\Entity\Catalogue\Piste;






class RechercheController extends AbstractController
{
	private $entityManager;
	private $logger;
	
	public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)  {
		$this->entityManager = $entityManager;
		$this->logger = $logger;
	}
	
    /**
     * @Route("/afficheRecherche", name="afficheRecherche")
     */
    public function afficheRechercheAction(Request $request)
    {
		$query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a");
		$articles = $query->getResult();
		$livres = $query->getResult();
		return $this->render('recherche.html.twig', [
            'articles' => $articles,
        ]);
    }


        /**
     * @Route("/admin/livres", name="adminLivres")
     */
    public function adminLivresAction(Request $request)
    {
		$query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Livre a");
		$articles = $query->getResult();
		return $this->render('admin.livres.html.twig', [
            'articles' => $articles,
        ]);
    }


    
/**
 * @Route("/search", name="search")
 */
public function search(Request $request, EntityManagerInterface $entityManager)
{
    $query = $entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a "
        ." where a.titre like '%".addslashes($request->query->get("motCle"))."%'");
    $articles = $query->getResult();

    return $this->render('results.html.twig', [
        'searchTerm' => $request->query->get("motCle"),
        'results' => $articles,
    ]);
}

    
    
	
    /**
     * @Route("/afficheRechercheParMotCle", name="afficheRechercheParMotCle")
     */
    public function afficheRechercheParMotCleAction(Request $request)
    {
		$query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a "
												  ." where a.titre like '%".addslashes($request->query->get("motCle"))."%'");
		$articles = $query->getResult();
		return $this->render('recherche.html.twig', [
            'articles' => $articles,
        ]);
    }
}
