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
	$limit_date = "2018-07-16 00:00:00.0";

	if (time() > strtotime($limit_date)) {
		?>
		<div class="centered_content">
			<div class="salutation">
				Nous sommes désolés !
			</div>
			<div class="answered_result">
				Il n'est plus possible de répondre à l'invitation par ce formulaire. Merci de nous contacter via le formulaire au bas de cette page.
			</div>
		</div>
		<?php
	} else {
		if (isset($_POST['just_answered'])) {
			$cocktail = $_POST['cocktail_answer'] ?: 'off';
			$diner = $_POST['diner_answer'] ?: 'off';
			$party = $_POST['party_answer'] ?: 'off';

			$cocktail = $cocktail == 'on' ? 1 : 0;
			$diner = $diner == 'on' ? 1 : 0;
			$party = $party == 'on' ? 1 : 0;
			
			$post_vars = filter_input_array(INPUT_POST);
			$co_answerers = preg_grep('/^answer_[0-9]+$/', array_keys($post_vars));

			$ids = array($_SESSION['id']);

			if (count($co_answerers) != 0) {
				foreach ($co_answerers as $co_answerer) {
					$co_id = str_replace('answer_', '', $co_answerer);
					array_push($ids, $co_id);
				}
			}

			$query = new dbQuery();
			$result = $query->setAnswerForIds($ids, $cocktail, $diner, $party);

			if ($result == 1) {
				$_SESSION['answered_sent'] = 1;
			} else {
				$_SESSION['error'] = "Il y a une erreur lors de l'envoi de votre réponse. Merci de réessayer plus tard ou de contacter les mariés en utilisant le formulaire situé au bas de cette page";
			}
		}

		if (isset($_SESSION['answered_sent'])) {
			unset($_SESSION['answered_sent']); // Pour ne pas réafficher le message à chaque rechargement de page
			?>
			<div id="overlay_background" onclick="off()">
				<div class="overlay green_overlay">
		  			<span>Réponse bien reçue !</span>
				</div>
			</div>
			<?php
		}

		if (isset($_SESSION['error'])) {
			?>
			<div id="overlay_background" onclick="off()">
				<div class="overlay green_overlay">
		  			<span>Erreur : <?php print($_SESSION['error']); ?><br />Si l'erreur persiste, contactez-nous avec le formulaire en bas de cette page.</span>
				</div>
			</div>
			<?php

			session_unset('error'); // Pour ne pas réafficher le message à chaque rechargement de page
		}

		if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['cp'])) {

			$query = new dbQuery();

			$result = $query->getDataWith($_SESSION["nom"], $_SESSION["prenom"], $_SESSION["cp"]);

			if ($result == NULL) {
				session_unset();

				session_destroy();
				?>
				<div class="centered_content">
					<div class="answered_result">
						Nous n'avons pas retrouvé votre invitation.
						<br />
						Si vous pensez que c'est une erreur n'hésitez pas à nous contacter via le formulaire au bas de cette page.
					</div>
				</div>
				<?php
				die();
			} else {
				$_SESSION['id'] = $result->id;
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
			
			<form action="#" method="POST">

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
				print('<p>Tu ne seras pas accompagné(e)</p>');
			} else {
				?>
				<table class="co-invitee-table">
					<tr>
						<th colspan="2">Je serai accompagné de : <a class="tooltip" href="#"><img class="help_icon" src='../img/help.png' /><span class="classic">Passez l'indicateur sur oui pour indiquer que cette personne vous accompagnera au mariage. Passez l'indicateur sur non pour indiquer qu'elle ne sera pas présente. </span></a></th>
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
								<input class="co-switch-input" type="checkbox" name="answer_<?php echo($co_invitee->id); ?>" <?php
									if ($co_invitee->hasSameAttendanceAs($result)) {
										print('checked ');
									}
									?>
								/>
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
			<span class="error_question">Tu penses qu’il y a une erreur avec ton invitation ? Contacte nous au <a href="#Contact">bas de cette page</a></span>
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
				<div class="answered_result">
					Pour accéder au formulaire de réponse, merci de compléter les informations suivantes :
				</div>
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
		} 
	} ?>
	<script type="text/javascript">
		function off() {
		    document.getElementById("overlay_background").style.display = "none";
		}
	</script>
</body>
</html>
   