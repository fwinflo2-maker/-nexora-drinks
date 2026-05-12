<?php
require_once('../Connexion.php');

?>
<script language="javascript" type="text/javascript">
window.location.replace("../index.php?formulaire=Consultation_Brouillard&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Sld=<?php echo $_POST["solde"] ;?>");
</script>