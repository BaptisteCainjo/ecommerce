<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Catalogue\Livre;
use App\Entity\Catalogue\Musique;

class AdminController extends AbstractController
{
	private $entityManager;
	private $logger;
	
	public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)  {
		$this->entityManager = $entityManager;
		$this->logger = $logger;
	}
	
    /**
     * @Route("/admin/musiques", name="adminMusiques")
     */
    public function adminMusiquesAction(Request $request)
    {
		$query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Musique a");
		$articles = $query->getResult();
		return $this->render('admin.musiques.html.twig', [
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
     * @Route("/admin/musiques/supprimer", name="adminMusiquesSupprimer")
     */
    public function adminMusiquesSupprimerAction(Request $request)
    {
		$entityArticle = $this->entityManager->getReference("App\Entity\Catalogue\Article", $request->query->get("id"));
		if ($entityArticle !== null) {
			$this->entityManager->remove($entityArticle);
			$this->entityManager->flush();
		}
		return $this->redirectToRoute("adminMusiques") ;
    }
	
    /**
     * @Route("/admin/livres/supprimer", name="adminLivresSupprimer")
     */
    public function adminLivresSupprimerAction(Request $request)
    {
		$entityArticle = $this->entityManager->getReference("App\Entity\Catalogue\Article", $request->query->get("id"));
		if ($entityArticle !== null) {
			$this->entityManager->remove($entityArticle);
			$this->entityManager->flush();
		}
		return $this->redirectToRoute("adminLivres") ;
    }

    /**
     * @Route("/admin/livres/ajouter", name="adminLivresAjouter")
     */
    public function adminLivresAjouterAction(Request $request)
    {
		$entity = new Livre() ;
		$formBuilder = $this->createFormBuilder($entity);
		$formBuilder->add("titre", TextType::class) ;
		$formBuilder->add("auteur", TextType::class) ;
		$formBuilder->add("prix", NumberType::class) ;
		$formBuilder->add("disponibilite", IntegerType::class) ;
		$formBuilder->add("image", TextType::class) ;
		$formBuilder->add("Info", TextType::class) ;
		$formBuilder->add("nbPages", IntegerType::class) ;
		$formBuilder->add("dateDeParution", TextType::class) ;
		$formBuilder->add("valider", SubmitType::class) ;
		// Generate form
		$form = $formBuilder->getForm();
		
		$form->handleRequest($request) ;
		
		if ($form->isSubmitted()) {
			$entity = $form->getData() ;
			$entity->setId(uniqid());
			$this->entityManager->persist($entity);
			$this->entityManager->flush();
			return $this->redirectToRoute("adminLivres") ;
		}
		else {
			return $this->render('admin.form.html.twig', [
				'form' => $form->createView(),
			]);
		}
    }
	
    /**
     * @Route("/admin/musiques/ajouter", name="adminMusiquesAjouter")
     */
    public function adminMusiquesAjouterAction(Request $request)
    {
		$entity = new Musique() ;
		$formBuilder = $this->createFormBuilder($entity);
		$formBuilder->add("titre", TextType::class, [
			'label' => 'Titre du projet',
			'attr' => [
				'placeholder' => 'Le titre du projet...',
			]
		]) ;
		$formBuilder->add("artiste", TextType::class, [
			'label' => "Nom de l'artiste",
			'data' => 'Orelsan',
		]) ;
		$formBuilder->add("prix", NumberType::class, [
			'label' => "Prix de l'article",
			'data' => '9.99',
		]) ;
		$formBuilder->add("disponibilite", IntegerType::class, [
			'label' => "Nombre d'article restant",
			'data' => '1',
		]) ;
		$formBuilder->add("image", UrlType::class, [
			'label' => "Lien de la pochette",
			'data' => 'https://www.printing.com/global/images/PublicShop/ProductSearch/prodgr_default_300.png',
		]) ;
		$formBuilder->add("dateDeParution", BirthdayType::class, [
			'label' => "Année de sortie",
		]) ;
		$formBuilder->add("valider", SubmitType::class, [
			'label' => "Ajouter",
		]) ;
		// Generate form
		$form = $formBuilder->getForm();
		
		$form->handleRequest($request) ;
		
		if ($form->isSubmitted()) {
			$entity = $form->getData() ;
			$entity->setId(uniqid());
			$this->entityManager->persist($entity);
			$this->entityManager->flush();
			return $this->redirectToRoute("adminMusiques") ;
		}
		else {
			return $this->render('admin.form.html.twig', [
				'form' => $form->createView(),
			]);
		}
    }

    /**
     * @Route("/admin/livres/modifier", name="adminLivresModifier")
     */
    public function adminLivresModifierAction(Request $request)
    {
		$entity = $this->entityManager->getReference("App\Entity\Catalogue\Livre", $request->query->get("id"));
		if ($entity === null) 
			$entity = $this->entityManager->getReference("App\Entity\Catalogue\Livre", $request->request->get("id"));
		if ($entity !== null) {
			$formBuilder = $this->createFormBuilder($entity);
			$formBuilder->add("id", HiddenType::class) ;
			$formBuilder->add("titre", TextType::class) ;
			$formBuilder->add("auteur", TextType::class) ;
			$formBuilder->add("prix", NumberType::class) ;
			$formBuilder->add("disponibilite", IntegerType::class) ;
			$formBuilder->add("image", TextType::class);
			$formBuilder->add("infoComp", TextType::class);
			$formBuilder->add("infoProduct", TextType::class);
			$formBuilder->add("dateDeParution", TextType::class) ;
			$formBuilder->add("valider", SubmitType::class) ;
			// Generate form
			$form = $formBuilder->getForm();
			
			$form->handleRequest($request) ;
			
			if ($form->isSubmitted()) {
				$entity = $form->getData() ;
				$this->entityManager->persist($entity);
				$this->entityManager->flush();
				return $this->redirectToRoute("adminLivres") ;
			}
			else {
				return $this->render('admin.form.html.twig', [
					'form' => $form->createView(),
				]);
			}
		}
		else {
			return $this->redirectToRoute("adminLivres") ;
		}
    }
	
    /**
     * @Route("/admin/musiques/modifier", name="adminMusiquesModifier")
     */
    public function adminMusiquesModifierAction(Request $request)
    {
		$entity = $this->entityManager->getReference("App\Entity\Catalogue\Musique", $request->query->get("id"));
		if ($entity === null) 
			$entity = $this->entityManager->getReference("App\Entity\Catalogue\Musique", $request->request->get("id"));
		if ($entity !== null) {
			$formBuilder = $this->createFormBuilder($entity);
			
			$formBuilder->add("titre", TextType::class, [
				'label' => 'Titre du projet',
			]) ;
			$formBuilder->add("artiste", TextType::class, [
				'label' => "Nom de l'artiste",
			]) ;
			$formBuilder->add("prix", NumberType::class, [
				'label' => "Prix de l'article",
				'scale' => 2,
				'attr' => array(
				  "min" => 0,
				  "maxlength" => 5,
				  "scale" => 2,
				  "step" => 0.01,
				  "placeholder" => "0.00",
				)
			]) ;
			$formBuilder->add("disponibilite", IntegerType::class, [
				'label' => "Nombre d'article restant",
				'attr' => [
					'min' => '0',
					'max' => '200'
				]
			]) ;
			$formBuilder->add("image", UrlType::class, [
				'label' => "Lien de la pochette",
                'attr' => [
                    'placeholder' => 'https://image.jpg',
                    'pattern' => '^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$'
                ]
			]) ;
			$formBuilder->add("dateDeParution", BirthdayType::class, [
				'label' => "Année de sortie",
				
			]) ;
			$formBuilder->add("valider", SubmitType::class, [
				'label' => "Modifier",
			]) ;
			// Generate form
			$form = $formBuilder->getForm();
			
			$form->handleRequest($request) ;
			
			if ($form->isSubmitted()) {
				$entity = $form->getData() ;
				$this->entityManager->persist($entity);
				$this->entityManager->flush();
				return $this->redirectToRoute("adminMusiques") ;
			}
			else {
				return $this->render('admin.form.html.twig', [
					'form' => $form->createView(),
				]);
			}
		}
		else {
			return $this->redirectToRoute("adminMusiques") ;
		}
    }
}
