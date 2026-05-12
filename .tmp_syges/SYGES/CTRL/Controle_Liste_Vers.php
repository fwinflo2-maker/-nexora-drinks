<?php
session_start();
require_once('../Connexion.php');

	  ?>
	  <script language="javascript" type="text/javascript">
		  window.location.replace("../index.php?formulaire=Consultation_Liste_Vers&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&user=<?php echo $_POST["codeuser"] ;?>");
	  </script>
	  <?php

?>