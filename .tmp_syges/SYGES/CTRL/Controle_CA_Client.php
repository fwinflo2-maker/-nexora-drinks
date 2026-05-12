<?php
require_once('../Connexion.php');

?>
<script language="javascript" type="text/javascript">
window.location.replace("../index.php?formulaire=Consultation_CA_Client&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>");
</script>