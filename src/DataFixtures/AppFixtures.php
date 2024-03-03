<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\ApaiIO;
use ApaiIO\Operations\Search as AmazonSearch;
use ApaiIO\Request\GuzzleRequestWithoutKeys;
use GuzzleHttp\Client;

use DeezerAPI\Search as DeezerSearch;

use App\Entity\Catalogue\Livre;
use App\Entity\Catalogue\Musique;
use App\Entity\Catalogue\Piste;
use DateTime;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
		if (count($manager->getRepository("App\Entity\Catalogue\Article")->findAll()) == 0) {
			$conf = new GenericConfiguration();
			$client = new Client();
			$request = new GuzzleRequestWithoutKeys($client);

			try {
				/*$conf
					->setCountry('de')
					->setAccessKey(AWS_API_KEY)
					->setSecretKey(AWS_API_SECRET_KEY)
					->setAssociateTag(AWS_ASSOCIATE_TAG);*/
				$conf
					->setCountry('fr')
					->setRequest($request) ;
			} catch (\Exception $e) {
				echo $e->getMessage();
			}
			$apaiIO = new ApaiIO($conf);

			$search = new AmazonSearch();
			$search->setCategory('Music');
			$keywords = 'Orelsan' ;
			
			//$search->setCategory('Books');
			//$keywords = 'Henning Mankell' ;
			
			$search->setKeywords($keywords);
			
			$search->setResponseGroup(array('Offers','ItemAttributes','Images'));

			$formattedResponse = $apaiIO->runOperation($search);

			$xml = simplexml_load_string($formattedResponse);
			if ($xml !== false) {
				$indice = 0;
				foreach ($xml->children() as $child_1) {
					if ($child_1->getName() === "Items") {
						foreach ($child_1->children() as $child_2) {
							if ($indice <= 7) {
							$indice = $indice+ 1;
							if ($child_2->getName() === "Item") {
								if ($child_2->ItemAttributes->ProductGroup->__toString() === "Book") {
									$entityLivre = new Livre();
									$entityLivre->setId($child_2->ASIN);
									$entityLivre->setTitre($child_2->ItemAttributes->Title);
									$entityLivre->setAuteur($child_2->ItemAttributes->Author);
									$entityLivre->setInfoProduct($child_2->ItemAttributes->Info);
									$entityLivre->setPrix($child_2->OfferSummary->LowestNewPrice->Amount/100.0); 
									$entityLivre->setDisponibilite(1);
									$entityLivre->setImage($child_2->LargeImage->URL);
									$manager->persist($entityLivre);
									$manager->flush();
								}
								if ($child_2->ItemAttributes->ProductGroup->__toString() === "Music") {
									$entityMusique = new Musique();
									$entityMusique->setId($child_2->ASIN);
									$entityMusique->setTitre($child_2->ItemAttributes->Title);
									$entityMusique->setArtiste($child_2->ItemAttributes->Artist);
									// $entityMusique->setDateDeParution($child_2->ItemAttributes->PublicationDate);
									if (DateTime::createFromFormat('Y', (string) $child_2->ItemAttributes->PublicationDate) !== false) {
										$date = DateTime::createFromFormat('Y', (string) $child_2->ItemAttributes->PublicationDate);
										$entityMusique->setDateDeParution($date);
									} else {
										$entityMusique->setDateDeParution(null);
									}

									// $entityMusique->setDateDeParution(new \DateTime('now'));

									$entityMusique->setPrix($child_2->OfferSummary->LowestNewPrice->Amount/100.0); 
									$entityMusique->setDisponibilite(50);
									$entityMusique->setImage($child_2->LargeImage->URL) ;
									if (!isset($albums)) {
										$deezerSearch = new DeezerSearch($keywords);
										$artistes = $deezerSearch->searchArtist() ;
										$albums = $deezerSearch->searchAlbumsByArtist($artistes[0]->getId()) ;
									}
									$j = 0 ;
									$sortir = ($j==count($albums)) ;
									$albumTrouve = false ;
									while (!$sortir) {
										$titreDeezer = str_replace(" ","",mb_strtolower($albums[$j]->title)) ;
										$titreAmazon = str_replace(" ","",mb_strtolower($entityMusique->getTitre())) ;
										$titreDeezer = str_replace("-","",$titreDeezer) ;
										$titreAmazon = str_replace("-","",$titreAmazon) ;
                                        $albumTrouve = ($titreDeezer == $titreAmazon) ;
                                        if (mb_strlen($titreAmazon) > mb_strlen($titreDeezer))
                                            $albumTrouve = $albumTrouve || (mb_strpos($titreAmazon, $titreDeezer) !== false) ;
                                         if (mb_strlen($titreDeezer) > mb_strlen($titreAmazon))
                                            $albumTrouve = $albumTrouve || (mb_strpos($titreDeezer, $titreAmazon) !== false) ;
										$j++ ;
										$sortir = $albumTrouve || ($j==count($albums)) ;
									}
									if ($albumTrouve) {
										$tracks = $deezerSearch->searchTracksByAlbum($albums[$j-1]->getId()) ;
										foreach ($tracks as $track) {
											$entityPiste = new Piste();
											$entityPiste->setTitre($track->title);
											$entityPiste->setMp3($track->preview);
											$manager->persist($entityPiste);
											$manager->flush();
											$entityMusique->addPiste($entityPiste) ;
										}
									}
									$manager->persist($entityMusique);
									$manager->flush();
								}
							}
						}
					}
				}
				}


				$entityLivre = new Livre();
				$entityLivre->setId(1);
				$entityLivre->setTitre("[T-SHIRT] Civilisation Perdue noir");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Le t-shirt noir en coton doux est un incontournable de la garde-robe, polyvalent et confortable. Conçu spécialement pour la réédition Civilisation Perdue, sa coupe ajustée et son col rond offrent une allure moderne et intemporelle, idéale pour un look décontracté et tendance.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen / 100% coton / Grammage: 165 g/m2");
				$entityLivre->setDateDeParution("2022");
				$entityLivre->setPrix("24.90");
				$entityLivre->setDisponibilite(25);
				$entityLivre->setImage("/images/Mockup-TSNoir-CivilisationPerdue.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] Drapeau Civilisation blanc");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Le t-shirt blanc en coton doux est un incontournable de la garde-robe, polyvalent et confortable. Imprimer en 2021, lors de la sortie du projet Civilisation, le design avec le drapeau, mais également sa coupe ajustée et son col rond offrent une allure moderne et intemporelle, idéale pour un look décontracté et tendance.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen / 100% coton / Grammage: 165 g/m2 / Si vous hésitez entre deux tailles, choisissez la plus grande
				Taille");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("27.90");
				$entityLivre->setDisponibilite(10);
				$entityLivre->setImage("/images/Mockup-TSBlanc-DrapeauxEtoile-Front.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] Texte noir");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Le t-shirt noir avec un texte en blanc sur du coton doux est un incontournable de la garde-robe, polyvalent et confortable. Ce texte explique les couleurs des drapeaux du projet, une pièce unique pour une allure moderne et intemporelle, idéale pour un look décontracté et tendance.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen / 100% coton / Grammage: 165 g/m2 / Si vous hésitez entre deux tailles, choisissez la plus grande");
				$entityLivre->setDateDeParution("2022");
				$entityLivre->setPrix("24.90");
				$entityLivre->setDisponibilite(10);
				$entityLivre->setImage("/images/Mockup-TSNoir-TextexCivilisationperdue.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] Texte blanc");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Le t-shirt blanc avec un texte en noir sur du coton doux est un incontournable de la garde-robe, polyvalent et confortable. Ce texte explique les couleurs des drapeaux du projet, une pièce unique pour une allure moderne et intemporelle, idéale pour un look décontracté et tendance.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen / 100% coton / Grammage: 165 g/m2 / Si vous hésitez entre deux tailles, choisissez la plus grande");
				$entityLivre->setDateDeParution("2022");
				$entityLivre->setPrix("24.90");
				$entityLivre->setDisponibilite(10);
				$entityLivre->setImage("/images/Mockup-TSBlanc-TextexCivilisationperdue.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] Drapeau Civilisation noir");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Le t-shirt noir en coton doux est un incontournable de la garde-robe, polyvalent et confortable. Imprimer en 2021, lors de la sortie du projet Civilisation, le design avec le drapeau, mais également sa coupe ajustée et son col rond offrent une allure moderne et intemporelle, idéale pour un look décontracté et tendance.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen / 100% coton / Grammage: 165 g/m2 / Si vous hésitez entre deux tailles, choisissez la plus grande");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("27.90");
				$entityLivre->setDisponibilite(10);
				$entityLivre->setImage("/images/Mockup-TSNoir-DrapeauxEtoile-Front.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] Civilisation Perdue blanc");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Le t-shirt noir en coton doux est un incontournable de la garde-robe, polyvalent et confortable. Conçu spécialement pour la réédition Civilisation Perdue, sa coupe ajustée et son col rond offrent une allure moderne et intemporelle, idéale pour un look décontracté et tendance.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen100% cotonGrammage: 165 g/m2
				Si vous hésitez entre deux tailles, choisissez la plus grand");
				$entityLivre->setDateDeParution("2022");
				$entityLivre->setPrix("24.90");
				$entityLivre->setDisponibilite(25);
				$entityLivre->setImage("/images/Mockup-TSBlanc-CivilisationPerdue.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] Shuriken brodé");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Le T-shirt spécial d'Orelan est un must-have pour tous les fans qui cherchent à afficher leur soutien avec style. Avec une broderie 3D unique, ce T-shirt est à la fois tendance et original. Fabriqué à partir de matériaux de qualité supérieure, il est confortable à porter et facile à entretenir. Que ce soit pour une occasion décontractée ou plus habillée, ce T-shirt est un choix polyvalent et indémodable.");
				$entityLivre->setInfoComp("T-shirt avec broderie 3D100% coton / Grammage: 165 g/m2Brodé à Caen / 4 couleurs de broderie au choix (rouge, vert, bleu, noir) /
				Si vous hésitez entre deux tailles, choisissez la plus grande");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("27.90");
				$entityLivre->setDisponibilite(25);
				$entityLivre->setImage("/images/orelsanmerch_shuriken_tshirt_bleu.webp");
				$manager->persist($entityLivre);


				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] CD noir");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Le t-shirt noir en coton doux est un incontournable de la garde-robe, polyvalent et confortable. Avec son design consititué des pochettes disponible lors de la sortie du projet, mais sa coupe ajustée et son col rond offrent une allure moderne et intemporelle, idéale pour un look décontracté et tendance.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen / 100% coton Grammage: 165 g/m2");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("24.90");
				$entityLivre->setDisponibilite(25);
				$entityLivre->setImage("/images/tshirtnoir.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] CD blanc");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Le t-shirt blanc en coton doux est un incontournable de la garde-robe, polyvalent et confortable. Avec son design consititué des pochettes disponible lors de la sortie du projet, mais sa coupe ajustée et son col rond offrent une allure moderne et intemporelle, idéale pour un look décontracté et tendance.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen / 100% coton Grammage: 165 g/m2");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("24.90");
				$entityLivre->setDisponibilite(25);
				$entityLivre->setImage("/images/tshirtblanc.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] Casseurs Flowters blanc");
				$entityLivre->setAuteur("Casseurs Flowters");
				$entityLivre->setInfoProduct("T-Shirt collector du duo, le t-shirt en blanc est conçu en coton doux est un incontournable de la garde-robe, polyvalent et confortable.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen 100% coton / Grammage: 165 g/m");
				$entityLivre->setDateDeParution("2013");
				$entityLivre->setPrix("24.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Orelsan-classiques_Plandetravail1.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] Casseurs Flowters noir");
				$entityLivre->setAuteur("Casseurs Flowters");
				$entityLivre->setInfoProduct("T-Shirt collector du duo, le t-shirt en noir est conçu en coton doux est un incontournable de la garde-robe, polyvalent et confortable.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen 100% coton / Grammage: 165 g/m");
				$entityLivre->setDateDeParution("2013");
				$entityLivre->setPrix("24.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Orelsan-classiques-02.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] Perdu d'avance blanc");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("T-Shirt collector d'Orelsan, le t-shirt en noir est conçu en coton doux est un incontournable de la garde-robe, polyvalent et confortable.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen / 100% coton / Grammage: 165 g/m2 / Si vous hésitez entre deux tailles, choisissez la plus grande");
				$entityLivre->setDateDeParution("2009");
				$entityLivre->setPrix("24.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Orelsan-classiques-03.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] Perdu d'avance bleu marine");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("T-Shirt collector d'Orelsan, le t-shirt en bleu marine est conçu en coton doux est un incontournable de la garde-robe, polyvalent et confortable.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen / 100% coton / Grammage: 165 g/m2 / Si vous hésitez entre deux tailles, choisissez la plus grande");
				$entityLivre->setDateDeParution("2009");
				$entityLivre->setPrix("24.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Orelsan-classiques-04.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] Logo personnage blanc");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("T-Shirt collector d'Orelsan, le t-shirt Logo personnage en blanc est conçu en coton doux est un incontournable de la garde-robe, polyvalent et confortable.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen 100% coton / Grammage: 165 g/m2 / Si vous hésitez entre deux tailles, choisissez la plus grand");
				$entityLivre->setDateDeParution("2009");
				$entityLivre->setPrix("24.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Orelsan-classiques-05.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[T-SHIRT] Logo personnage noir");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("T-Shirt collector d'Orelsan, le t-shirt Logo personnage en noir est conçu en coton doux est un incontournable de la garde-robe, polyvalent et confortable.");
				$entityLivre->setInfoComp("T-shirt imprimé à Caen 100% coton / Grammage: 165 g/m2 / Si vous hésitez entre deux tailles, choisissez la plus grand");
				$entityLivre->setDateDeParution("2009");
				$entityLivre->setPrix("24.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Orelsan-classiques-06.webp");
				$manager->persist($entityLivre);

				// Bonnets

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[Bonnet] Civilisation Perdue");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Ce bonnet est chaud, confortable et tendance représente spécialement la réédition Civilisation Perdue sortie en 2022. Fabriqué avec des matériaux de haute qualité, il est polyvalent et peut être porté pour ajouter une touche d'originalité à une tenue plus formelle.");
				$entityLivre->setInfoComp("Bonnet brodé à Caen / 100% acrylique / Taille unique");
				$entityLivre->setDateDeParution("2022");
				$entityLivre->setPrix("17.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Mockup-Bonnet-CivilisationPerdue.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[Bonnet] Drapeau Civilisation");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Ce bonnet est le premier exemplaire de la version initial du 4e projet solo d'Orelan. Avec son design avec le drapeau spécialement conçu par le rappeur, il est chaud, confortable et tendance.");
				$entityLivre->setInfoComp("Bonnet brodé à Caen / 100% acrylique / Taille unique");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("17.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Mockup-Bonnet-Drapeau.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[Bonnet] Shuriken");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Chaud, confortable et tendance, vous trouverez sur ce bonnet le shuriken qu'on l'on retrouve tout du long du projet. Fabriqué avec des matériaux de haute qualité, il est polyvalent et peut être porté pour ajouter une touche d'originalité à une tenue plus formelle.");
				$entityLivre->setInfoComp("Bonnet brodé à Caen / 100% acrylique / Taille unique");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("17.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Mockup-Bonnet-Etoile.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[Bonnet] Shuriken 3D rouge");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Chaud, confortable, tendance et surtout ROUGE, vous trouverez sur ce bonnet le shuriken qu'on l'on retrouve tout du long du projet mais cette fois-ci en 3D. Fabriqué avec des matériaux de haute qualité, il est polyvalent et peut être porté pour ajouter une touche d'originalité à une tenue plus formelle.");
				$entityLivre->setInfoComp("Bonnet brodé à Caen100% acrylique / Taille unique");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("17.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Orelsanmerch_bonnetrouge_1.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[Bonnet] Shuriken 3D noir");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Chaud, confortable, tendance et surtout NOIR, vous trouverez sur ce bonnet le shuriken qu'on l'on retrouve tout du long du projet mais cette fois-ci en 3D. Fabriqué avec des matériaux de haute qualité, il est polyvalent et peut être porté pour ajouter une touche d'originalité à une tenue plus formelle.");
				$entityLivre->setInfoComp("Bonnet brodé à Caen / 100% acrylique / Taille unique");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("17.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Orelsanmerch_bonnetnoir_1.webp");
				$manager->persist($entityLivre);

				// Casquette

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[Casquette] Shuriken");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Accessoire tendance et unique, vous trouverez sur cette casquette le shuriken qu'on l'on retrouve tout du long du projet. Confectionnée avec des matériaux de qualité, cette casquette est dotée d'une broderie unique qui représente l'univers créatif d'Orelsan.");
				$entityLivre->setInfoComp("Casquette brodée à Caen / Brodée en France / Taille unique");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("22.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Mockup-Casquette-Etoile-Face.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[Casquette] Civilisation Perdue");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Cette casquette est l'accessoire tendance, unique et  représente spécialement la réédition Civilisation Perdue sortie en 2022.Confectionnée avec des matériaux de qualité, cette casquette est dotée d'une broderie unique qui représente l'univers créatif d'Orelsan.");
				$entityLivre->setInfoComp("Casquette brodée à Caen / Brodée en France / Taille unique");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("22.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Mockup-Casquette-CivilisationPerdue-Face.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[Casquette] Drapeau Civilisation");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Cette casquette est le premier exemplaire de la version initial du 4e projet solo d'Orelan. Avec son design avec le drapeau spécialement conçu par le rappeur, il est tendance et unique.");
				$entityLivre->setInfoComp("Casquette brodée à Caen / Brodée en France / Taille unique");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("22.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/Mockup-Casquette-Drapeau-Face.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[Casquette] Shuriken 3D brodée");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("La casquette spéciale d'Orelsan arbore une broderie 3D unique qui fera ressortir votre style. Cette casquette est un accessoire polyvalent qui complétera parfaitement votre look décontracté ou ajoutera une touche d'originalité à une tenue plus habillée. Avec sa broderie en relief, cette casquette est un must-have pour tous les fans de l'artiste.");
				$entityLivre->setInfoComp("Casquette avec broderie 3D / Brodée à Caen / 4 couleurs de broderie au choix (rouge, vert, bleu, noir)
				");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("22.90");
				$entityLivre->setDisponibilite(5);
				$entityLivre->setImage("/images/orelsanmerch_black_cap_green.webp");
				$manager->persist($entityLivre);


				// MUG

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[Mug] Drapeau Civilisation");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Optez pour l'originalité avec le mug spécial d'Orelsan avec le drapeau Civilisation que vous connaissez très bien maintenant. En céramique de qualité, son design unique fera de chaque boisson un moment exceptionnel. Un must-have pour tous les amateurs de mode et de culture.");
				$entityLivre->setInfoComp("Mug imprimé à Caen / Taille unique");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("13.90");
				$entityLivre->setDisponibilite(10);
				$entityLivre->setImage("/images/Mockup-Tasse-Drapeau.webp");
				$manager->persist($entityLivre);


				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[Mug] Shuriken");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Optez pour l'originalité avec le mug spécial d'Orelsan. En toute simplicité, vous retrouvez le shuriken disposé sur le mug. En céramique de qualité, son design unique fera de chaque boisson un moment exceptionnel. Un must-have pour tous les amateurs de mode et de culture.");
				$entityLivre->setInfoComp("Mug imprimé à Caen / Taille unique");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("13.90");
				$entityLivre->setDisponibilite(10);
				$entityLivre->setImage("/images/Mockup-Tasse-Etoile.webp");
				$manager->persist($entityLivre);

				$entityLivre = new Livre();
				$entityLivre->setId(uniqid());
				$entityLivre->setTitre("[Hoodie] Shuriken brodé");
				$entityLivre->setAuteur("Orelsan");
				$entityLivre->setInfoProduct("Le hoodie spécial d'Orelsan est un incontournable pour tous les fans de mode streetwear. Avec sa broderie 3D unique, ce sweatshirt à capuche apporte une touche de style supplémentaire à n'importe quelle tenue. Fabriqué avec des matériaux de haute qualité, ce sweatshirt est à la fois confortable et tendance.");
				$entityLivre->setInfoComp("Sweatshirt à capuche avec broderie 3D / 85% coton bio ring-spun / 15% polyester recyclé / Grammage: 350 g/m2 / Brodé à Caen / 4 couleurs de broderie au choix (rouge, vert, bleu, noir)");
				$entityLivre->setDateDeParution("2021");
				$entityLivre->setPrix("57.90");
				$entityLivre->setDisponibilite(15);
				$entityLivre->setImage("/images/om_hoodie_black.webp");
				$manager->persist($entityLivre);








				$manager->flush();
			}
		}
    }
}
