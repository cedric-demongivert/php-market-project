<?php

class Ariane extends Controller {
	
	public function __construct() {
		parent::__construct("ariane", "Ariane.template.html");
		$this->title = "Fil d'ariane";
	}
	
	public function getCategoryTree() {
		
		if(isset($_GET['id_category'])) {
			
			/* Récupération de la catégorie courrante : */
			$category = new Category();
			$category = $category->selectById($_GET['id_category']);
			
			if(!empty($category)) {
				/* Tableau contenant les parents : */
				$tree = array($category);
				
				/* Tant qu'on peut récupérer un parent : */
				while(($category = $category->getParent()) != null) {
					$tree[] = $category;
				}
				
				/* Tableau retourné */
				$returnable = array();
	
				/* On inverse $tree */
				for($i = 1; $i <= sizeof($tree); $i++) {
					$returnable[] = $tree[sizeof($tree)-$i];
				}
				
				return $returnable;
			}
			else {
				return array();
			}
			
		}/* isset($_GET['id_category']) */
		
		return array();
		
	}
	
	
}

?>