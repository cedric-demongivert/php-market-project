<?php
require_once "./controllers/Ariane.class.php";

class PanierController extends Controller {
	
	public function __construct() {
		parent::__construct("panier", "Panier.template.html");
		$this->title = "Panier";
		
		if (!isset($_SESSION['panier'])) {
			$_SESSION['panier'] = new Panier();
		} 

	}
	
	public function getArticles() {
		
		$panier = $_SESSION['panier'];
		
		$results = array();
		
		$article = new Article();
		
		foreach($panier->getItems() as $item) {
			
			$results[] = $article->selectById($item->getIdArticle());
			
		}
		
		$results = Model::toData($results);
		
		$i = 0;
		foreach($panier->getItems() as $item) {
				
			$results[$i]['nombre'] = $item->getQuantity();
			$i++;
			
		}
		 
		return $results;
		
	}
	
	public function getArticle($id) {
		$article = new Article();
		if (isset($_SESSION['panier'])) {
			$article->selectById($id);
		} else {
			$article->setId($id);
		}
		return $article;
	}
	
	public function ajouter() {
		$article = new Article();
		$panier = $_SESSION['panier'];
		if (isset($_GET['id_article'])) {
			if (($article->selectById($_GET['id_article'])) != null) {
				$panier->addItem($_GET['id_article'],1);
				header('Location:./index.php?service=PanierController');
				exit();
			}
		}
	}
	
	public function remove() {
		if (isset($_SESSION['panier']) && isset($_GET['id_article'])) {
			$_SESSION['panier']->remove($_GET['id_article']);
		}
	}
	
	public function addQuantity() {
		$panier = $_SESSION['panier'];
		if (isset($_GET['id_article'])) {
			$panier->addItem($_GET['id_article'], $_POST['nombre']);
		}
	}
	
	public function validate() {

		if (isset($_SESSION['user']) && $this->getModifState() == 0) {
			
			$article = new Article();
			$commande = new Commande();
			$commande->setIdUser($_SESSION['user']->getId());
			$commande->insert();
			
			$reservation = new Reservation();
			foreach ($_SESSION['panier']->getItems() as $item) {
				$article = $article->selectById($item->getIdArticle());
				if ($item->getQuantity() > $article->getNombre()) {
					$commande->delete();
					header('Location: index.php?service=PanierController&modif=1');
					exit();
				}
				$reservation->setIdArticle($article->getId());
				$reservation->setIdCommande($commande->getId());
				$reservation->setNombre($item->getQuantity());
				$reservation->setPrix($item->getQuantity()*$article->getPrix()*(1 - $article->getRemise()/100.0));
				$reservation->insert();
				$article->setNombre($article->getNombre()-$item->getQuantity());
				$article->update();
			}
			
			$_SESSION['panier'] = new Panier();
			$this->setInfo("Votre commande a bien été enregistrée ! ");
			header('Location: index.php?service=PanierController');
		} else {
			header('Location: index.php?service=Users');
			exit();
		}
	}
	
	public function getModifState() {
		
		if(!isset($_GET['modif'])) {
			return 0;
		}
		
		return $_GET['modif'];
	}
	
	public function modif() {
		$article = new Article();
		foreach ($_SESSION['panier']->getItems() as $item) {
			$article = $article->selectById($item->getIdArticle());
			if ($item->getQuantity() > $article->getNombre()) {
				$_SESSION['panier']->remove($article->getId());
				$_SESSION['panier']->addItem($article->getId(), $article->getNombre());
			}
		}
		/*header("Location: index.php?service=PanierController");
		exit();*/
	}
	
}

?>