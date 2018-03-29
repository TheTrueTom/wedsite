<?php
ini_set('display_errors', 'On');

include('rsvp.php');



$query = new dbQuery();

$result = $query->getDataWith($_POST["nom"], $_POST["prenom"], $_POST["cp"]);

print('<br /><br />');
print('Bienvenue ' . $result->prenom . ' !');
print('<br /><br />');
print('Merci de prendre le temps de répondre !');
print('<br /><br />');

if ($result->isCocktailInvited()) {
	print('Tu es invité au cocktail, et tu as répondu : ');
	if ($result->hasAnswered()) {
		if ($result->isCocktailComing()) {
			print('oui');
		} else {
			print('non');
		}
	} else {
		print('-');
	}
}

print('<br /><br />');

if ($result->isDinerInvited()) {
	print('Tu es invité au dîner, et tu as répondu : ');
	if ($result->hasAnswered()) {
		if ($result->isDinerComing()) {
			print('oui');
		} else {
			print('non');
		}
	} else {
		print('-');
	}
}

print('<br /><br />');

if ($result->isPartyInvited()) {
	print('Tu es invité à la soirée, et tu as répondu : ');
	if ($result->hasAnswered()) {
		if ($result->isPartyComing()) {
			print('oui');
		} else {
			print('non');
		}
	} else {
		print('-');
	}
}

print('<br /><br />');
$co_invitees = $result->coInvitees();

if (count($co_invitees) == 0) {
	print('Tu ne seras pas accompagné.');
} else {
	print('Tu seras accompagné de :');
	print('<br />');
	
	foreach ($co_invitees as $co_invitee) {
		print($co_invitee->prenom . ' ' . $co_invitee->nom);

		if ($co_invitee->isChild()) {
			print(' - inscrit en tant qu\'enfant');
		}

		print('<br />');
	}
}

print('<br /><br />');
?>
