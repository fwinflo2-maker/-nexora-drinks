<?php
session_start();
require_once('../Connexion.php');
if (isset($_POST['Avance']) && isset($_POST['Paye']))
	{	
		$statut="Mixte";
	}
	else
		if (isset($_POST['Avance']))
		{
			$statut="Avance";
		}
		else
			{
				$statut="Paye";
			}
			?>
<script language="javascript" type="text/javascript">
	window.location.replace("../index.php?formulaire=Consultation_Liste_Reglement&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Stat=<?php echo $statut ;?>");
</script>
  