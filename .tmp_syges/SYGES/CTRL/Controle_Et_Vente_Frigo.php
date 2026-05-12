<?php
session_start();
require_once('../Connexion.php');

		if($_SESSION['habilitation']=='Administrateur')
			{
				?>
                <script language="javascript" type="text/javascript">
				window.location.replace("../index.php?formulaire=Consultation_Et_Vente_Frigo&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>");
				</script>
				<?php
			}
			if	($_SESSION['habilitation']=='Gerant'  || $_SESSION['habilitation']=="Comptable")
			{
				?>
				 <script language="javascript" type="text/javascript">
				 window.location.replace("../index.php?formulaire=Consultation_Et_Vente_Frigo_Gerant&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>");
				</script>
			              <?php
			}

	?>