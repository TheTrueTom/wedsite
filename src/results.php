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
	$query = new dbQuery();
	$invitees = $query->getAllData();

	if ($invitees == NULL) {
		?>
		<div class='centered_message'>
			Soit il n'y a pas d'invités, soit il y a eu un problème lors de la récupération des informations.
			<p style="text-align: center;"><a href="fiche.php?add">Ajouter un invité</a></p>
		</div>
		<?php
		die();
	}
	?>
	<div class='main_container'>
		<h1>Réponses mariage Delphine & Thomas</h1>
		<input type="text" id="searchInput" onkeyup="filterNames()" placeholder="Chercher un nom ou un prénom ...">
		<table id='inviteesTable' class='invitees-table'>
			<tr>
				<th colspan='4'>Informations</th>
				<th colspan='7' onclick="resetFilter()">Réponses</th>
				<th></th>
			</tr>
			<tr>
				<th>Id</th>
				<th>Nom</th>
				<th>Prénom</th>
				<th onclick="filterAnswer(3)">Invité</th>
				<th onclick="filterAnswer(4)">Répondu</th>
				<th colspan='2' onclick="filterAnswer(6)">Cocktail</th>
				<th colspan='2' onclick="filterAnswer(8)">Dîner</th>
				<th colspan='2' onclick="filterAnswer(10)">Soirée</th>
				<th></th>
			</tr>
			<?php
			$invited_number = 0;
			$answered_number = 0;

			$cocktail_invited = 0;
			$coktail_number = 0;

			$diner_invited = 0;
			$diner_number = 0;

			$party_invited = 0;
			$party_number = 0;

			$coming_number = 0;

			foreach ($invitees as $invitee) {

				if ($invitee->isInvited()) { $invited_number++; }
				if ($invitee->hasAnswered()) { $answered_number++; }

				if ($invitee->isComing()) { $coming_number++; }

				if ($invitee->isCocktailInvited()) { $cocktail_invited++; }
				if ($invitee->isCocktailComing() && !$invitee->isChild()) { $cocktail_number++; }

				if ($invitee->isDinerInvited()) { $diner_invited++; }
				if ($invitee->isDinerComing() && !$invitee->isChild()) { $diner_number++; }

				if ($invitee->isPartyInvited()) { $party_invited++; }
				if ($invitee->isPartyComing() && !$invitee->isChild()) { $party_number++; }

			?>
				<tr class='invitee-row'>
					<td class='td-id'><?php print($invitee->id); ?></td>
					<td class='td-name'><?php print($invitee->nom); ?></td>
					<td class='td-firstname'><?php print($invitee->prenom); ?></td>
					<td class='td-answered <?php print(($invitee->isChild() ? 'blue' : ($invitee->isInvited() ? 'green' : 'red')) . '-background') ?>'><?php print(($invitee->isChild() ? 'Enfant' : ($invitee->isInvited() ? 'Oui' : 'Non'))) ?></td>
					<td class='td-answered <?php print((($invitee->answered) ? 'green' : 'red') . '-background') ?>'><?php print(($invitee->answered) ? 'Oui' : 'Non') ?></td>
					<td class='td-answer-indicator <?php print((($invitee->isCocktailInvited()) ? 'green' : 'red') . '-background') ?>'></td>
					<td class='td-answer <?php print((($invitee->isCocktailComing()) ? 'green' : 'red') . '-background') ?>'><?php print(($invitee->isCocktailComing()) ? 'Oui' : 'Non') ?></td>
					<td class='td-answer-indicator <?php print((($invitee->isDinerInvited()) ? 'green' : 'red') . '-background') ?>'></td>
					<td class='td-answer <?php print((($invitee->isDinerComing()) ? 'green' : 'red') . '-background') ?>'><?php print(($invitee->isDinerComing()) ? 'Oui' : 'Non') ?></td>
					<td class='td-answer-indicator <?php print((($invitee->isPartyInvited()) ? 'green' : 'red') . '-background') ?>'></td>
					<td class='td-answer <?php print((($invitee->isPartyComing()) ? 'green' : 'red') . '-background') ?>'><?php print(($invitee->isPartyComing()) ? 'Oui' : 'Non') ?></td>
					<td class='td-modify'><a href='fiche.php?id=<?php print($invitee->id); ?>'><img src="../img/edit.png" /></a></td>
				</tr>
			<?php
			}
			?>

			<tr class='results'>
				<td colspan="3" class='results-title'>Totaux</td>
				<td><?php print($invited_number); ?> / <?php print(count($invitees)); ?></td>
				<td colspan="2"><?php print($answered_number); ?> / <?php print($invited_number); ?></td>
				<td colspan="2"><?php print($cocktail_number); ?> / <?php print($cocktail_invited); ?></td>
				<td colspan="2"><?php print($diner_number); ?> / <?php print($diner_invited); ?></td>
				<td colspan="2"><?php print($party_number); ?> / <?php print($party_invited); ?></td>
			</tr>
		</table>
	</div>
	<p style="text-align: center;"><a href="fiche.php?add">Ajouter un invité</a></p>
	<script>
		function filterNames() {
		  // Declare variables 
		  var input, filter, table, tr, name, firstname, i;

		  input = document.getElementById("searchInput");
		  filter = input.value.toUpperCase();
		  table = document.getElementById("inviteesTable");
		  tr = table.getElementsByTagName("tr");

		  // Loop through all table rows, and hide those who don't match the search query
		  for (i = 0; i < tr.length; i++) {
		    name = tr[i].getElementsByTagName("td")[1];
		    firstname = tr[i].getElementsByTagName("td")[2];

		    if (name || firstname) {
		      if (name.innerHTML.toUpperCase().indexOf(filter) > -1 || firstname.innerHTML.toUpperCase().indexOf(filter) > -1) {
		        tr[i].style.display = "";
		      } else {
		      	if (i < tr.length - 1) { // Pour garder les totaux
		        	tr[i].style.display = "none";
		        }
		      }
		    }
		  }
		}

		function filterAnswer(column) {
		  var filter, table, tr, i, name;

		  filter = 'oui'.toUpperCase();
		  table = document.getElementById("inviteesTable");
		  tr = table.getElementsByTagName("tr");

		  // Loop through all table rows, and hide those who don't match the search query
		  for (i = 0; i < tr.length; i++) {
		    name = tr[i].getElementsByTagName("td")[column];

		    if (name) {
		      if (name.innerHTML.toUpperCase().indexOf(filter) > -1) {
		        tr[i].style.display = "";
		      } else {
		      	if (i < tr.length - 1) { // Pour garder les totaux
		        	tr[i].style.display = "none";
		        }
		      }
		    } 
		  }
		}

		function resetFilter() {
		  var table, tr, i;

		  table = document.getElementById("inviteesTable");
		  tr = table.getElementsByTagName("tr");

		  // Loop through all table rows, and hide those who don't match the search query
		  for (i = 0; i < tr.length; i++) {
		    tr[i].style.display = ""; 
		  }
		}
</script>
</body>
</html>������
