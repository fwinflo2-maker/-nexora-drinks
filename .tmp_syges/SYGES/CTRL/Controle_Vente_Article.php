<?php
session_start();
require_once('../Connexion.php');

			if($_SESSION['habilitation']=='Administrateur')
			{
				?>
				<script language="javascript" type="text/javascript">
                    window.location.replace("../index.php?formulaire=Consultation_Etat_Vente_Ar&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&user=<?php echo $_POST["codeuser"] ;?>");
                </script>
				<?php
			}
			if	($_SESSION['habilitation']=='Gerant' || $_SESSION['habilitation']=='OPS'  || $_SESSION['habilitation']=="Comptable")
			{
					?>
				<script language="javascript" type="text/javascript">
                    window.location.replace("../index.php?formulaire=Consultation_Etat_Vente_Ar_Gerant&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&user=<?php echo $_POST["codeuser"] ;?>");
                </script>
                <?php
			}

	?>