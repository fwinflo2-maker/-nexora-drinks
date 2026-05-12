<?php
if (isset($_POST['DateD']) && isset($_POST['DateF']))
	{	
			if($_POST['typemouv']=='Toutes')
			{
				?>
                <script language="javascript" type="text/javascript">
				window.location.replace("../index.php?formulaire=Consultation_Liste_Ann&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Ope=Toutes");
				</script>
                <?php
			}
					
			if($_POST['typemouv']=='Ventes')
			{
				?>
                <script language="javascript" type="text/javascript">
				window.location.replace("../index.php?formulaire=Consultation_Liste_Ann&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Ope=Ventes");
				</script>
                <?php
			}
			if	($_POST['typemouv']=='Sorties Cessions')
			{
					?>
                <script language="javascript" type="text/javascript">
				window.location.replace("../index.php?formulaire=Consultation_Liste_Ann&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Ope=Sorties Cessions");
				</script>
                <?php
			}
			if	($_POST['typemouv']=='Entrées Cessions')
			{
					?>
                <script language="javascript" type="text/javascript">
				window.location.replace("../index.php?formulaire=Consultation_Liste_Ann&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Ope=Entrées Cessions");
				</script>
                <?php
			}
			if	($_POST['typemouv']=='Approvisionnements')
			{
					?>
                <script language="javascript" type="text/javascript">
				window.location.replace("../index.php?formulaire=Consultation_Liste_Ann&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Ope=Approvisionnements");
				</script>
                <?php
			}
}
	?>