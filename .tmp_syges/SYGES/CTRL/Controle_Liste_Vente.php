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

					
			if($_SESSION['habilitation']=='Administrateur' || $_SESSION['habilitation']=='OPS' || $_SESSION['habilitation']=="Comptable")
			{
				?>
                <script language="javascript" type="text/javascript">
				window.location.replace("../index.php?formulaire=Consultation_Liste_Vente&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Stat=<?php echo $statut ;?>");
				</script>
                <?php
			}
			if	($_SESSION['habilitation']=='Gerant')
			{
					?>
                <script language="javascript" type="text/javascript">
				window.location.replace("../index.php?formulaire=Consultation_Liste_Vente_Gerant&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Stat=<?php echo $statut ;?>");
				</script>
                <?php
			}

	?>