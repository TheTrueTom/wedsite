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
			Il y a eu un problème lors de la récupération des informations.
		</div>
		<?php
		die();
	}
	?>
	<div class='main_container'>
		<table class='invitees-table'>
			<tr>
				<th colspan='4'></th>
				<th colspan='3'>Réponses</th>
				<th colspan='3'>Invitation</th>
			</tr>
			<tr>
				<th>Id</th>
				<th>Nom</th>
				<th>Prénom</th>
				<th>Répondu</th>
				<th>Cocktail</th>
				<th>Dîner</th>
				<th>Soirée</th>
				<th>Cocktail</th>
				<th>Dîner</th>
				<th>Soirée</th>
			</tr>
		</table>
	</div>
</body>
</html>                                                                                                          