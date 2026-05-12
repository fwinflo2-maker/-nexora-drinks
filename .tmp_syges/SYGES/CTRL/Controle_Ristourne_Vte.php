<?php
require_once('../Connexion.php');

?>
<script language="javascript" type="text/javascript">
window.location.replace("../index.php?formulaire=Consultation_Ristourne_Vte&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Retfr=<?php echo $_POST["Retfrigo"];?>&RetDA=<?php echo $_POST["RetDA"];?>&RetCGA=<?php echo $_POST["RetCGA"];?>&RegR=<?php echo $_POST["RegRistourne"];?>&RegPSAEC=<?php echo $_POST["RegPSAEC"];?>&RegPSAAnt=<?php echo $_POST["RegPSAAnt"];?>&RegDA=<?php echo $_POST["RegDA"];?>&RegEntfr=<?php echo $_POST["RegEntfrigo"];?>&RegCGA=<?php echo $_POST["RegCGA"];?>");
</script>