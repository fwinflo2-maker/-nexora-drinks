<?php
session_start();
require_once('../Connexion.php');

?>
<script language="javascript" type="text/javascript">
	window.location.replace("../index.php?formulaire=Consultation_Liste_Detaille_Vente_Client&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Clt=<?php echo $_POST["codeclt"];?> ");
</script>
                
