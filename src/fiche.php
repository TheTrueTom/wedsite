<?php
	ob_start();
	session_start();

	ini_set('display_errors', 'On');

	include('rsvp.php');
?>

<html>
<header>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="../css/result_style.css">
	<link href="https://fonts.googleapis.com/css?family=Quicksand:300, 400, 500, 700" rel="stylesheet" />
</header>
<body>
<?php
	if (isset($_GET['del'])) {
		$query = new dbQuery();
		$result = $query->deleteInvitee($_GET['id']);

		if ($result == 1) {
			header("Location:results.php");
		}
	}

	if (!isset($_GET['add'])) {
		if (isset($_POST['nom'])) {
			$id	 				= $_POST['id'];
			$nom 				= $_POST['nom'];
			$prenom 			= $_POST['prenom'];
			$address 			= $_POST['address'];
			$cp 				= $_POST['cp'];
			$ville 				= $_POST['ville'];
			$pays  				= $_POST['pays'];
			$email 				= $_POST['email'];
			$co_invites 		= explode(",", $_POST['co_invites']);

			$enfant 			= $_POST['enfant'] ?: 'off';
			$answered			= $_POST['answered'] ?: 'off';
			$cocktail_invite 	= $_POST['cocktail_invite'] ?: 'off';
			$diner_invite		= $_POST['diner_invite'] ?: 'off';
			$party_invite		= $_POST['party_invite'] ?: 'off';
			$cocktail_answer	= $_POST['cocktail_answer'] ?: 'off';
			$diner_answer		= $_POST['diner_answer'] ?: 'off';
			$party_answer		= $_POST['party_answer'] ?: 'off';
			$modifie_co_invites = $_POST['modifie_co_invites'] ?: 'off';

			$enfant 			= $enfant == 'on' ? 1 : 0;
			$answered			= $answered	== 'on' ? 1 : 0; 
			$cocktail_invite 	= $cocktail_invite == 'on' ? 1 : 0; 
			$diner_invite		= $diner_invite	== 'on' ? 1 : 0; 
			$party_invite		= $party_invite	== 'on' ? 1 : 0; 
			$cocktail_answer	= $cocktail_answer== 'on' ? 1 : 0; 
			$diner_answer		= $diner_answer	== 'on' ? 1 : 0; 
			$party_answer		= $party_answer	== 'on' ? 1 : 0; 
			$modifie_co_invites = $modifie_co_invites == 'on' ? 1 : 0;

			$co_invitee_string = '';
			
			foreach ($co_invites as $co_invitee) {
				$co_invitee_string .= $co_invitee . ',';
			}

			$co_invitee_string = rtrim($co_invitee_string, ','); 

			$data = array(	"nom"=>$nom,
							"prenom"=>$prenom,
							"address"=>$address,
							"cp"=>$cp,
							"ville"=>$ville,
							"pays"=>$pays,
							"email"=>$email,
							"enfant"=>$enfant,
							"answered"=>$answered,
							"cocktail_invite"=>$cocktail_invite,
							"diner_invite"=>$diner_invite,
							"party_invite"=>$party_invite,
							"cocktail_answer"=>$cocktail_answer,
							"diner_answer"=>$diner_answer,
							"party_answer"=>$party_answer,
							"co_invites"=>$co_invitee_string);

			$query = new dbQuery();

			if (isset($_POST['add'])) {
				$result = $query->addInvitee($data);

				if ($result != 0) {
					header("Location:fiche.php?id=" . $result);
				}
			} else {
				if (!$modifie_co_invites) {
					$result = $query->setDataForId($id, $data);
				} else {
					$result = $query->setDataForIdAndCo($id, $data, $co_invites);
				}

				header("Location:fiche.php?id=" . $id);
			}
			
		}

		$id = $_GET['id'];

		$query = new dbQuery();

		$invitee = $query->getDataWithId($id);
	} else {
		$invitee = new Invitee();
	}

	if ($invitee == NULL) {
		session_unset();

		session_destroy();
		?>
		<h1>Identifiant invalide</h1>
		<?php
		die();
	} else {
		print('<h1>' . $invitee->prenom . ' ' . $invitee->nom . '</h1>');
		print('<a href="results.php">Retourner à la liste</a>');
		?>
		<form action=<?php print((isset($_POST['add'])) ? "#" : ("http://" . $_SERVER['SERVER_NAME'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0])); ?> method="POST">
			<table id='inviteesTable' class='fiche_table'>
				<tr>
					<th colspan='8'>Informations</th>
				</tr>
				<tr>
					<th colspan='2'>Id</th>
					<th colspan='2'>Nom</th>
					<th colspan='2'>Prénom</th>
					<th colspan='2'>Enfant</th>
				</tr>
				<tr>
					<td colspan='2'><input type=hidden name="id" value='<?php print($invitee->id); ?>' /><?php print((isset($_GET['add'])) ? "Auto-généré" : $invitee->id); ?></td>
					<td colspan='2'><input type="text" name="nom" value="<?php print($invitee->nom); ?>"></input></td>
					<td colspan='2'><input type="text" name="prenom" value="<?php print($invitee->prenom); ?>"></input></td>
					<td colspan='2'><input type=checkbox name="enfant" <?php print(($invitee->isChild()) ? 'checked' : ''); ?>/></td>
				</tr>
				<tr>
					<th colspan='4'>Invitations</th>
					<th colspan='3'>Réponses</th>
					<th></th>
				</tr>
				<tr>
					<th>Répondu</th>
					<th>Cocktail</th>
					<th>Dîner</th>
					<th>Soirée</th>
					<th>Cocktail</th>
					<th>Dîner</th>
					<th>Soirée</th>
					<th>Co-invités</th>
				</tr>
				<tr>
					<td><input type=checkbox name="answered" <?php print(($invitee->hasAnswered()) ? 'checked' : ''); ?> /></td>
					<td><input type=checkbox name="cocktail_invite" <?php print(($invitee->isCocktailInvited()) ? 'checked' : ''); ?> /></td>
					<td><input type=checkbox name="diner_invite" <?php print(($invitee->isDinerInvited()) ? 'checked' : ''); ?> /></td>
					<td><input type=checkbox name="party_invite" <?php print(($invitee->isPartyInvited()) ? 'checked' : ''); ?> /></td>
					<td><input type=checkbox name="cocktail_answer" <?php print(($invitee->isCocktailComing()) ? 'checked' : ''); ?> /></td>
					<td><input type=checkbox name="diner_answer" <?php print(($invitee->isDinerComing()) ? 'checked' : ''); ?> /></td>
					<td><input type=checkbox name="party_answer" <?php print(($invitee->isPartyComing()) ? 'checked' : ''); ?> /></td>
					<td><input type="text"   name="co_invites" value="<?php print($invitee->coInviteesString()); ?>"></input></td>
				</tr>
				<tr>
					<th colspan='8'>Informations de contact</th>
				</tr>
				<tr>
					<th colspan='4'>Adresse</th>
					<th>CP</th>
					<th colspan='2'>Ville</th>
					<th>Pays</th>
				</tr>
				<tr>
					<td colspan="4"><input type="text" name="address" value="<?php print($invitee->address); ?>"></input></td>
					<td><input type="text" name="cp" value="<?php print($invitee->cp); ?>"></input></td>
					<td colspan='2'><input type="text" name="ville" value="<?php print($invitee->ville); ?>"></input></td>
					<td><input type="text" name="pays" value="<?php print($invitee->pays); ?>"></input></td>
				</tr>
				<tr>
					<th colspan='8'>E-mail</th>
				</tr>
				<tr>
					<td colspan="8"><input type="email" name="email" value="<?php print($invitee->email); ?>"></input></td>
				</tr>
			</table>
			<?php print((isset($_GET['add'])) ? '<input type=\'hidden\' name=\'add\' value=1 />' : '')?>
			<p style="text-align: center;">Modifier aussi les co-invités : <input type=checkbox name="modifie_co_invites" /><br />(Ne modifie pas les nom, prénom, email et statut d'enfant pour les co-invités)</p>
			<p style="text-align: center;"><input type="submit" value="<?php print((isset($_GET['add'])) ? 'Ajouter' : 'Modifier')?>" /></p>
			<p style="text-align: center;"><a href="fiche.php?id=<?php print($invitee->id); ?>&del" onclick="return confirm('Êtes-vous sûr ?')">Supprimer cet invité</a></p>
		</form>
		<?php
	}
?>
</body>
</html>
