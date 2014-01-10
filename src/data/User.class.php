<?php

class User {
	
	private $id;
	private $login;
	private $pass;
	private $admin;
	private $loaded;
	
	public static function create($login, $pass, $admin) {
		$user = new User();
		$user->login = $login;
		$user->pass = crypt($pass);
		$user->admin = $admin;
		$user->loaded = false;
	}
	
	public static function get($id, $bdd) {
		
		$user = new User();
		
		$user->id = $id;
		
		$result = $bdd->prepare("SELECT * FROM user WHERE id = :id");
		$result->bindParam(":id",$id);
		
		if(!$result->execute()) {
			print_r($result->errorInfo());
			die("Erreur SQL get User");
		}
		
		if ($data = $result->fetch()) {
			$user->login = $data["login"];
			$user->pass = $data["pass"];
			$user->admin = $data["admin"];
			$user->loaded = true;
		} else {
			die("L'utilisateur qui possède l'id $id n'existe pas.");
		}
		
		return $user;
		
	}
	
	public static function exist($login, $pass, $bdd) {
		
		$result = $bdd->prepare("SELECT * FROM user WHERE login = :login AND pass = :pass");
		$result->bindParam(":login",$login);
		$result->bindParam(":pass",crypt($pass));
		
		if(!$result->execute()) {
			print_r($result->errorInfo());
			die("Erreur SQL get User");
		}
		
		if($result->rowCount() >= 1) {
			$data = $result->fetch();
			return $data["id"];
		}
		
		return -1;
		
	}
	
	private function __construct() {
		
	}
	
	public function isAdmin() {
		return $this->admin;
	}
	
	public function getLogin() {
		return $this->login;
	}
	
	public function getPass() {
		return $this->pass;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setAdmin($admin) {
		$this->admin = $admin;
	}
	
	public function setLogin($login) {
		$this->login = $login;
	}
	
	public function setPass($pass) {
		$this->pass = $pass;
	}
	
}

?>