<?php
require_once('../Connexion.php');

?>
<script language="javascript" type="text/javascript">
	window.location.replace("../Chiffre_Affaire.php?debut=<?php echo $_POST["DateD"];?>&fin=<?php echo $_POST["DateF"];?>");
</script>