<?php
require_once "./controllers/Ariane.class.php";
require_once "./controllers/PanierController.class.php";

class Categories extends Controller {
	
	public function __construct() {
		parent::__construct("nav", "Categories.template.html");
		$this->title = "Navigation";
		$this->includeController(new Ariane("index.php?service=Categories"));
		$this->includeController(new PanierController("index.php?service=Categories"));
	}
	
	public function init() {
		
	}
	
	public function getCategory() {
		$category = new Category();
		if (isset($_GET['id_category']) && $_GET['id_category'] != -1) {
			return $category->selectById($_GET['id_category']);
		} else {
			$category->setId(-1);
			$category->setNom("racine");
			return $category;
		}
	}
	
	public function getCategories() {
		$category = new Category();
		if (isset($_GET['id_category'])) {
			$categories = $category->select("idParent = ".Model::$bdd->quote($_GET['id_category']));
			
			return $categories;
		} else {
			$categories = $category->select("idParent = -1");
			
			return $categories;
		}
	}
	
	public function getArticles() {
		$category = $this->getCategory();
		
		return $category->getSpecificArticles();
	}
	
}

?>