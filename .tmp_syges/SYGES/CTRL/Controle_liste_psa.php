<?php
require_once('../Connexion.php');

?>
<script language="javascript" type="text/javascript">
	window.location.replace("../index.php?formulaire=Consultation_liste_psa&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Cat=<?php echo $_POST["codecat"] ;?>");
</script>