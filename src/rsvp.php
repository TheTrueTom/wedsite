<?php

class dbQuery {
	private $host = 'mysql51-29.perso';
	private $username = 'brichartovh';
	private $password = 'qf3ewp6';
	private $dbname = 'brichartovh';
	private $email = 'thomas@brichart.fr';
	private $table_name = 'tbdv_invites';

	function getDataWith($nom, $prenom, $cp) {

		$link = mysqli_connect($this->host, $this->username, $this->password, $this->dbname);
		
		if (mysqli_connect_errno()) {
			echo("Failed to connect to database : " . mysqli_connect_errno());
			exit();
		}

		$sql = "SELECT * FROM " . $this->table_name . " WHERE nom = '" . $nom . "' AND prenom = '" . $prenom . "' AND cp = " . $cp . " LIMIT 1";
		$result = mysqli_query($link,$sql);

		if($result == FALSE) { 
    		return NULL;
		}

		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

		mysqli_free_result($result);

		mysqli_close($link);

		if ($row["nom"] == NULL) {
			return NULL;
		}

		$invitee = new Invitee();
		$invitee->fillInfoWith($row);

		return $invitee;
	}

	function getDataWithId($id) {
		$link = mysqli_connect($this->host, $this->username, $this->password, $this->dbname);
		
		if (mysqli_connect_errno()) {
			echo("Failed to connect to database : " . mysqli_connect_errno());
			exit();
		}

		$sql = "SELECT * FROM " . $this->table_name . " WHERE id = " . $id . " LIMIT 1";
		$result = mysqli_query($link,$sql);

		if($result == FALSE) { 
    		return NULL;
		}

		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

		mysqli_free_result($result);

		mysqli_close($link);

		$invitee = new Invitee();
		$invitee->fillInfoWith($row);

		return $invitee;
	}
}

class Invitee {
	public $id	 				= 0;
	public $nom 				= '';
	public $prenom 				= '';
	public $address 			= '';
	public $cp 					= 0;
	public $ville 				= '';
	public $pays  				= '';
	public $email 				= '';
	public $co_invites 			= array();
	public $enfant 				= FALSE;
	public $answered			= FALSE;
	public $cocktail_invite 	= FALSE;
	public $diner_invite		= FALSE;
	public $party_invite		= FALSE;
	public $cocktail_answer		= FALSE;
	public $diner_answer		= FALSE;
	public $party_answer		= FALSE;

	function fillInfoWith($row) {
		$this->id 				= $row['id'];
		$this->nom 				= $row['nom'];
		$this->prenom 			= $row['prenom'];
		$this->address 			= $row['address'];
		$this->cp 				= $row['cp'];
		$this->ville 			= $row['ville'];
		$this->pays 			= $row['pays'];
		$this->email 			= $row['email'];

		$co_invites_string = $row["co_invites"];

		if (co_invites_string != '') {
			$co_invites_string = str_replace(' ', '', $co_invites_string);
			$this->co_invites 		= explode(",", $co_invites_string);
		}

		$this->enfant 			= $row['enfant'];
		$this->answered 		= $row['answered'];
		$this->cocktail_invite 	= $row['cocktail_invite'];
		$this->diner_invite 	= $row['diner_invite'];
		$this->party_invite 	= $row['party_invite'];
		$this->cocktail_answer 	= $row['cocktail_answer'];
		$this->diner_answer 	= $row['diner_answer'];
		$this->party_answer 	= $row['party_answer'];
	}

	function coInvitees() {
		if (count($this->co_invites) == 0) {
			return array();
		}

		$co_invitees = array();

		foreach($this->co_invites as $co_id)
		{
			$query = new dbQuery();
			$co_invitee = $query->getDataWithId($co_id);

			array_push($co_invitees, $co_invitee);
		}

		return $co_invitees;
	}

	function isCocktailInvited() {
		return $this->cocktail_invite;
	}

	function isDinerInvited() {
		return $this->diner_invite;
	}

	function isPartyInvited() {
		return $this->party_invite;
	}

	function isCocktailComing() {
		return $this->cocktail_answer;
	}

	function isDinerComing() {
		return $this->diner_answer;
	}

	function isPartyComing() {
		return $this->party_answer;
	}

	function hasAnswered() {
		return $this->answered;
	}

	function isChild() {
		return $this->enfant;
	}
}
?>
