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
	if (isset($_POST['id'])) {
		$id	 				= $_POST['id'];
		$nom 				= $_POST['nom'];
		$prenom 			= $_POST['prenom'];
		$address 			= $_POST['address'];
		$cp 				= $_POST['cp'];
		$ville 				= $_POST['ville'];
		$pays  				= $_POST['pays'];
		$email 				= $_POST['email'];
		$co_invites 		= explode("-", $_POST['co_invites']);

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

		$data = array(	"address"=>$address,
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
						"party_answer"=>$party_answer);
		
		$ids = array();

		if ($modifie_co_invites) {

			$ids = $co_invites;
		}

		array_push($ids, $id);

		$query = new dbQuery();
		$result = $query->setDataForIds($ids, $data);
	}

	$id = $_GET['id'];

	$query = new dbQuery();

	$invitee = $query->getDataWithId($id);

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
		<form action="#" method="POST">
			<table id='inviteesTable' class='invitees-table'>
				<tr>
					<th colspan='4'>Informations</th>
					<th colspan='4'>Invitations</th>
				</tr>
				<tr>
					<th>Id</th>
					<th>Nom</th>
					<th>Prénom</th>
					<th>Enfant</th>
					<th>Répondu</th>
					<th>Cocktail</th>
					<th>Dîner</th>
					<th>Soirée</th>
				</tr>
				<tr>
					<td class='td-id td-edit'><input type=hidden name="id" value='<?php print($invitee->id); ?>' /><?php print($invitee->id); ?></td>
					<td class='td-name td-edit'><?php print($invitee->nom); ?></td>
					<td class='td-name td-edit'><?php print($invitee->prenom); ?></td>
					<td class='td-answer td-edit'><input type=checkbox name="enfant" <?php print(($invitee->isChild()) ? 'checked' : ''); ?> disabled="disabled" /></td>
					<td class='td-answer td-edit'><input type=checkbox name="answered" <?php print(($invitee->hasAnswered()) ? 'checked' : ''); ?> /></td>
					<td class='td-answer td-edit'><input type=checkbox name="cocktail_invite" <?php print(($invitee->isCocktailInvited()) ? 'checked' : ''); ?> /></td>
					<td class='td-answer td-edit'><input type=checkbox name="diner_invite" <?php print(($invitee->isDinerInvited()) ? 'checked' : ''); ?> /></td>
					<td class='td-answer td-edit'><input type=checkbox name="party_invite" <?php print(($invitee->isPartyInvited()) ? 'checked' : ''); ?> /></td>
				</tr>
				<tr>
					<th colspan='5'>Informations supplémentaires</th>
					<th colspan='3'>Réponses</th>
				</tr>
				<tr>
					<th colspan='2'>Adresse</th>
					<th>CP</th>
					<th>Ville</th>
					<th>Pays</th>
					<th>Cocktail</th>
					<th>Dîner</th>
					<th>Soirée</th>
				</tr>
				<tr>
					<td class='td-name td-edit' colspan="2"><input type="text" name="address" value="<?php print($invitee->address); ?>"></input></td>
					<td class='td-name td-edit'><input type="text" name="cp" value="<?php print($invitee->cp); ?>"></input></td>
					<td class='td-name td-edit'><input type="text" name="ville" value="<?php print($invitee->ville); ?>"></input></td>
					<td class='td-name td-edit'><input type="text" name="pays" value="<?php print($invitee->pays); ?>"></input></td>
					<td class='td-answer td-edit'><input type=checkbox name="cocktail_answer" <?php print(($invitee->isCocktailComing()) ? 'checked' : ''); ?> /></td>
					<td class='td-answer td-edit'><input type=checkbox name="diner_answer" <?php print(($invitee->isDinerComing()) ? 'checked' : ''); ?> /></td>
					<td class='td-answer td-edit'><input type=checkbox name="party_answer" <?php print(($invitee->isPartyComing()) ? 'checked' : ''); ?> /></td>
				</tr>
			</table>
			<input type=hidden value=<?php 
				$co_invitee_string = '';
				foreach ($invitee->co_invites as $co_invitee) {
					$co_invitee_string .= $co_invitee . '-';
				}

				$co_invitee_string = rtrim($co_invitee_string, '-');

				print($co_invitee_string);
				?> name='co_invites' />
			<p style="text-align: center;">Modifier aussi les co-invités : <input type=checkbox name="modifie_co_invites" /></p>
			<p style="text-align: center;"><input type="submit" value="Modifier" /></p>
		</form>
		<?php
	}
?>
</body>
</html>                                                            