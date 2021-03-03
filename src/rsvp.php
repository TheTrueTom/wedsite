<?php

class dbQuery {
	private $host = 'mysql51-29.perso';
	private $username = 'brichartovh';
	private $password = 'qf3ewp6';
	private $dbname = 'brichartovh';
	private $email = 'thomas@brichart.fr';
	private $table_name = 'tbdv_invites';

	function getAllData() {
		$link = mysqli_connect($this->host, $this->username, $this->password, $this->dbname);
		$link->set_charset("utf8");
		
		if (mysqli_connect_errno()) {
			echo("Failed to connect to database : " . mysqli_connect_errno());
			exit();
		}

		$sql = "SELECT * FROM " . $this->table_name . " ORDER BY id LIMIT 1000";
		$result = mysqli_query($link, $sql);

		if($result == FALSE) { 
    		return NULL;
		}

		$invitees = array();

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$invitee = new Invitee();
			$invitee->fillInfoWith($row);
			array_push($invitees, $invitee);
		}

		mysqli_free_result($result);

		mysqli_close($link);

		return $invitees;
	}

	function getDataWith($nom, $prenom, $cp) {

		$link = mysqli_connect($this->host, $this->username, $this->password, $this->dbname);
		$link->set_charset("utf8");

		if (mysqli_connect_errno()) {
			echo("Failed to connect to database : " . mysqli_connect_errno());
			exit();
		}

		$sql = "SELECT * FROM " . $this->table_name . " WHERE nom = _utf8'" . $nom . "' AND prenom = _utf8'" . $prenom . "' AND cp = " . $cp . " LIMIT 1";
		$result = mysqli_query($link, $sql);

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
		$link->set_charset("utf8");
		
		if (mysqli_connect_errno()) {
			echo("Failed to connect to database : " . mysqli_connect_errno());
			exit();
		}

		$sql = "SELECT * FROM " . $this->table_name . " WHERE id = " . $id . " LIMIT 1";
		$result = mysqli_query($link, $sql);

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

	function setAnswerForIds($id_list, $cocktail, $diner, $party) {
		foreach ($id_list as $id) {
			$result = $this->setAnswerForId($id, $cocktail, $diner, $party);

			if ($result != 1) {
				return 0;
			}
		}

		return 1;
	}

	function setAnswerForId($id, $cocktail, $diner, $party) {
		$link = mysqli_connect($this->host, $this->username, $this->password, $this->dbname);

		if (mysqli_connect_errno()) {
			echo("Failed to connect to database : " . mysqli_connect_errno());
			exit();
		}

		$sql = "UPDATE " . $this->table_name . " SET answered = 1, cocktail_answer = " . $cocktail . ", diner_answer = " . $diner . ", party_answer = " . $party . " WHERE id = " . $id;
		$result = mysqli_query($link, $sql);

		return $result;
	}

	function setDataForId($id, $data) {
		$link = mysqli_connect($this->host, $this->username, $this->password, $this->dbname);

		if (mysqli_connect_errno()) {
			echo("Failed to connect to database : " . mysqli_connect_errno());
			exit();
		}

		$sql = "UPDATE " . $this->table_name . 	" SET " . 
												((array_key_exists("nom", $data)) ? "nom = _utf8\"{$data["nom"]}\", " : "") .
												((array_key_exists("prenom", $data)) ? "prenom = _utf8\"{$data["prenom"]}\", " : "") .
												((array_key_exists("email", $data)) ? "email = _utf8\"{$data["email"]}\", " : "") .
												"address = _utf8\"" . $data["address"] . "\", " .
												"co_invites = _utf8\"" . $data["co_invites"] . "\", " .
												((array_key_exists("email", $data)) ? "enfant = {$data["enfant"]}, " : "") .
												"cp = " . $data["cp"] . ", " .
												"ville = _utf8\"" . $data["ville"] . "\", " . 
												"pays = _utf8\"" . $data["pays"] . "\", " . 
												"answered = " . $data["answered"] . ", " .
												"cocktail_invite = " . $data["cocktail_invite"] . ", " . 
												"diner_invite = " . $data["diner_invite"] . ", " . 
												"party_invite = " . $data["party_invite"] . ", " . 
												"cocktail_answer = " . $data["cocktail_answer"] . ", " . 
												"diner_answer = " . $data["diner_answer"] . ", " . 
												"party_answer = " . $data["party_answer"] . 
												" WHERE id = " . $id;

		$result = mysqli_query($link, $sql);
		
		if ( !empty( $error = mysqli_error($link) ) )
		{
		    echo 'Mysql error '. $error ."<br />\n";
		}

		return $result;
	}

	function setDataForIdAndCo($id, $data, $co_invites) {
		$result = $this->setDataForId($id, $data);

		if ($result != 1) {
			return 0;
		}

		$newData = $data;
		unset($newData["nom"]);
		unset($newData["prenom"]);
		unset($newData["email"]);
		unset($newData["enfant"]);

		$old_co_invites = $newData["co_invites"];

		foreach ($co_invites as $co_id) {
			$newData["co_invites"] = $this->setCoIds($id, $co_id, $old_co_invites);

			$result = $this->setDataForId($co_id, $newData);

			if ($result != 1) {
				return 0;
			}
		}

		return 1;
	}

	function setCoIds($originId, $selfId, $co_ids) {
		$new_co_ids = explode(",", $co_ids);

		$selfIdIndex = array_search($selfId, $new_co_ids);

		unset($new_co_ids[$selfIdIndex]);

		array_push($new_co_ids, $originId);

		$co_invitee_string = '';
		
		foreach ($new_co_ids as $co_invitee) {
			$co_invitee_string .= $co_invitee . ',';
		}

		$co_invitee_string = rtrim($co_invitee_string, ','); 

		return $co_invitee_string;
	}

	function addInvitee($data) {
		$link = mysqli_connect($this->host, $this->username, $this->password, $this->dbname);

		if (mysqli_connect_errno()) {
			echo("Failed to connect to database : " . mysqli_connect_errno());
			exit();
		}

		$sql = " INSERT INTO " . $this->table_name . " (id, nom, prenom, address, cp, ville, pays, email, co_invites, enfant, answered, cocktail_invite, diner_invite, party_invite, cocktail_answer, diner_answer, party_answer) VALUES (NULL, " .
				((!empty($data["nom"])) 			? "_utf8'{$data["nom"]}', " : "NULL, ") .
				((!empty($data["prenom"])) 			? "_utf8'{$data["prenom"]}', " : "NULL, ") .
				((!empty($data["address"])) 		? "_utf8'{$data["address"]}', " : "NULL, ") .
				((!empty($data["cp"])) 				? "{$data["cp"]}, " : "0, ") .
				((!empty($data["ville"])) 			? "_utf8'{$data["ville"]}', " : "NULL, ") .
				((!empty($data["pays"])) 			? "_utf8'{$data["pays"]}', " : "NULL, ") .
				((!empty($data["email"])) 			? "_utf8'{$data["email"]}', " : "NULL, ") .
				((!empty($data["co_invites"])) 		? "{$data["co_invites"]}, " : "0, ") .
				((!empty($data["enfant"])) 			? "{$data["enfant"]}, " : "0, ") .
				((!empty($data["answered"])) 		? "{$data["answered"]}, " : "0, ") .
				((!empty($data["cocktail_invite"])) ? "{$data["cocktail_invite"]}, " : "0, ") .
				((!empty($data["diner_invite"]))	? "{$data["diner_invite"]}, " : "0, ") .
				((!empty($data["party_invite"])) 	? "{$data["party_invite"]}, " : "0, ") .
				((!empty($data["cocktail_answer"])) ? "{$data["cocktail_answer"]}, " : "0, ") .
				((!empty($data["diner_answer"])) 	? "{$data["diner_answer"]}, " : "0, ") .
				((!empty($data["party_answer"])) 	? "{$data["party_answer"]})" : "0)");

		$result = mysqli_query($link, $sql);
		
		if ( !empty( $error = mysqli_error($link) ) )
		{
		    echo 'Mysql error '. $error ."<br />\n";
		    return 0;
		} else {
			$last_id = $link->insert_id;
			return $last_id;
		}
	}

	function deleteInvitee($id) {
		$link = mysqli_connect($this->host, $this->username, $this->password, $this->dbname);

		if (mysqli_connect_errno()) {
			echo("Failed to connect to database : " . mysqli_connect_errno());
			exit();
		}

		$sql = "DELETE FROM " . $this->table_name . " WHERE id = " . $id;

		$result = mysqli_query($link, $sql);
		
		if ( !empty( $error = mysqli_error($link) ) )
		{
		    echo 'Mysql error '. $error ."<br />\n";
		}

		return $result;
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
		
		if ($co_invites_string != '' && !empty($co_invites_string)) {
			$co_invites_string  = str_replace(' ', '', $co_invites_string);
			$this->co_invites 	= explode(",", $co_invites_string);
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

	function coInviteesString() {
		$co_invitee_string = '';
		
		foreach ($this->co_invites as $co_invitee) {
			$co_invitee_string .= $co_invitee . ',';
		}

		$co_invitee_string = rtrim($co_invitee_string, ','); 

		return $co_invitee_string;
	}

	function isInvited() {
		return ($this->isCocktailInvited() || $this->isDinerInvited() || $this->isPartyInvited());
	}

	function isComing() {
		return ($this->isCocktailComing() || $this-> isDinerComing() || $this->isPartyComing());
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

	function hasSameAttendanceAs($otherInvitee) {
		return ($this->cocktail_answer == $otherInvitee->cocktail_answer && $this->cocktail_answer == $otherInvitee->cocktail_answer && $this->cocktail_answer == $otherInvitee->cocktail_answer);
	}
}
?>
