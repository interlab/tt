<?php $color = (isset($_GET['color'])) ? (int)$_GET['color'] : "084079"; ?>

	/* Bitmap smiley background */
	.color
		{ background-color: #<?php print $color; ?>; }