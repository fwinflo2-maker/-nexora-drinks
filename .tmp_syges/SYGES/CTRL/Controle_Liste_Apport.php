<?php
session_start();
require_once('../Connexion.php');
if (isset($_POST['Instance']) && isset($_POST['Valide']))
	{	
		$statut="Mixte";
	}
	else
		if (isset($_POST['Instance']))
		{
			$statut="N";
		}
		else
			{
				$statut="V";
			}

?>
        <script language="javascript" type="text/javascript">
			window.location.replace("../index.php?formulaire=Consultation_Etat_Apport&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Stat=<?php echo $statut ;?>");
		</script>
