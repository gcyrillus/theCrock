<?php
	# On vide la valeur de session qui affiche le Wizard maintenant qu'il est visible.
	if (isset($_SESSION['justactivated'.basename(__DIR__)])) {unset($_SESSION['justactivated'.basename(__DIR__)]);}
	?>
	wizard fr