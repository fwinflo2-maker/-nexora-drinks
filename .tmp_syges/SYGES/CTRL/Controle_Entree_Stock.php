<?php
require_once('../Connexion.php');
?>
<script language="javascript" type="text/javascript">
	window.location.replace("../index.php?formulaire=Consultation_Etat_Entree&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>");
</script>