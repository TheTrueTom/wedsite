<?php
	ob_start();
	session_start();

	ini_set('display_errors', 'On');

	include('rsvp.php');
?>

<html>
<header>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="../css/form_style.css">
	<link href="https://fonts.googleapis.com/css?family=Quicksand:300, 400, 500, 700" rel="stylesheet" />
	<link href="https://fonts.googleapis.com/css?family=Annie+Use+Your+Telescope" rel="stylesheet">
</header>
<body>
	<?php
	if (isset($_POST['just_answered'])) {
		/* Faire ce qu'il faut pour envoyer la réponse */
		print("Répondu");

		$_SESSION['answered_sent'] = 1;
		// OU
		$_SESSION['error'] = "Message d'erreur";
		header('Refresh: 0');
	}

	if (isset($_SESSION['answered_sent'])) {
		session_unset('answered_sent'); // Pour ne pas réafficher le message à chaque rechargement de page

		// Afficher un truc pour dire que la réponse a bien été envoyé
	}

	if (isset($_SESSION['error'])) {
		session_unset('error'); // Pour ne pas réafficher le message à chaque rechargement de page

		// Afficher un truc pour dire qu'il y a eu une erreur'
	}

	if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['cp'])) {

		$query = new dbQuery();

		$result = $query->getDataWith($_SESSION["nom"], $_SESSION["prenom"], $_SESSION["cp"]);

		if ($result == NULL) {
			session_unset();

			session_destroy();
			print('Nous n\'avons pas retrouvé votre invitation. Si vous pensez que c\'est une erreur n\'hésitez pas à contacter les mariés via le formulaire de contact en bas de cette page.');
			die();
		}
		?>
	<div class="centered_content">
		<div class="salutation">
			Bonjour <?php print($result->prenom); ?> !<br />
			<a class="not_me_prompt" href="logout.php">(Je ne suis pas <?php print($result->prenom); ?>)</a>
		</div>
		<div class="answered_result">
			<?php if ($result->hasAnswered()) { ?>
			Nous avons déjà reçu une réponse de ta part, celle-ci est reprise ci-dessous, tu peux encore la modifier
			<?php } else { ?>
			Nous n'avons pas encore eu de réponse de ta part, tu peux utiliser le module ci-dessous pour répondre.
			<?php } ?>
		</div>
		<div class="explanation">
			Clique sur les ronds ci-dessous pour indiquer ta présence, un cercle vert indique que tu seras présent.
		</div>
		
		<form action="test_page.php" method="POST">
		
		<?php

		if ($result->isCocktailInvited()) {
			?>
			<div class="div_container">
				<label class="switch switch-yes-no">
					<input class="switch-input" type="checkbox" name="cocktail_answer" <?php
						if ($result->hasAnswered()) {
							if ($result->isCocktailComing()) {
								print('checked ');
							}
						}
						?>
					/>
					<span class="switch-label switch-cocktail" data-on="" data-off=""></span> 
				</label>
				<div class="switch-description">Cocktail</div>
			</div>
		<?php
		}

		if ($result->isDinerInvited()) {
			?>
			<div class="div_container">
				<label class="switch switch-yes-no diner">
					<input class="switch-input" type="checkbox" name="diner_answer" <?php
						if ($result->hasAnswered()) {
							if ($result->isDinerComing()) {
								print('checked ');
							}
						}
						?>
					/>
					<span class="switch-label switch-diner" data-on="" data-off=""></span> 
				</label><br />
				<div class="switch-description">Dîner</div>
			</div>
		<?php
		}

		if ($result->isPartyInvited()) {
			?>
			<div class="div_container">
				<label class="switch switch-yes-no">
					<input class="switch-input" type="checkbox" name="party_answer" <?php
						if ($result->hasAnswered()) {
							if ($result->isPartyComing()) {
								print('checked ');
							}
						}
						?>
					/>
					<span class="switch-label switch-party" data-on="" data-off=""></span> 
				</label><br />
				<div class="switch-description">Soirée</div>
			</div>
		<?php
		}

		print('<br /><br />');
		$co_invitees = $result->coInvitees();

		if (count($co_invitees) == 0) {
			print('Tu ne seras pas accompagné.');
		} else {
			?>
			<table class="co-invitee-table">
				<tr>
					<th colspan="2">Je serais accompagné de :</th>
				</tr>
			<?php
			foreach ($co_invitees as $co_invitee) {
				?>
				<tr> <?php
				print('<td>' . $co_invitee->prenom . ' ' . $co_invitee->nom);

				if ($co_invitee->isChild()) {
					print(' (enfant)');
				}
				?>

					</td>
					<td>
						<label class="co-switch co-switch-left-right">
							<input class="co-switch-input" type="checkbox" name="<?php echo($co_invitee->id); ?>_answer" />
							<span class="co-switch-label" data-on="Oui" data-off="Non"></span> 
							<span class="co-switch-handle"></span> 
						</label>
					</td>
				</tr>
			<?php } ?>
			</table>
		<?php } ?>

		<input type="hidden" name="just_answered">
		<input type="submit" href="#" class="action-button shadow animate blue" />

		</form>
	</div>
		<?php

		print('</div>');

	} else if (isset($_POST["nom"]) && isset($_POST["prenom"]) && isset($_POST["cp"])) {
		if ($_POST["nom"] == '' or $_POST["prenom"] == '' or $_POST["cp"] == '') {
			print('Toutes les informations sont nécessaires pour retrouver votre invitation');
		} else {
			$_SESSION["nom"] = $_POST["nom"];
			$_SESSION["prenom"] = $_POST["prenom"];
			$_SESSION["cp"] = $_POST["cp"];

			header('Refresh: 0');
		}
	} else {
		?>
		<div class="centered_content">
			<form action="#" method="POST">
				<div class="group">      
				    <input type="text" name="nom" required>
				    <span class="highlight"></span>
				    <span class="bar"></span>
				    <label class="input-label">Nom</label>
			    </div>
			    <div class="group">      
				    <input type="text" name="prenom" required>
				    <span class="highlight"></span>
				    <span class="bar"></span>
				    <label class="input-label">Prénom</label>
			    </div>
			    <div class="group">      
				    <input type="number" name="cp" required>
				    <span class="highlight"></span>
				    <span class="bar"></span>
				    <label class="input-label">Code postal</label>
			    </div>
			 	<div class="group">
			 		<button type="submit" class="final-btn" value="➔">
			 			<svg viewBox="40 40 40 40" preserveAspectRatio="none" width="100%" height="100%" >
          					<path fill="#fff" className="arrow" d="M48.5,61.5v-2.9h17.4l-8-8l2.1-2.1L71.5,60L60,71.5l-2.1-2.1l8-8H48.5z" />
        				</svg>
        			</button>
			 	</div>
			</form>
		</div>
		<?php
	} ?>
</body>
</html>
