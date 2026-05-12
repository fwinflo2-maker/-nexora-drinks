<?php
if (isset($_POST['DateD']) && isset($_POST['DateF']))
	{	
					
			if($_POST['typemouv']=='Entrée en Stock')
			{
				?>
                <script language="javascript" type="text/javascript">
				window.location.replace("../index.php?formulaire=Consultation_Entree_St_Emb&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Emb=<?php echo $_POST['codeemb'] ;?>");
				</script>
                <?php
			}
			if	($_POST['typemouv']=='Sortie de Stock')
			{
				?>
                <script language="javascript" type="text/javascript">
				window.location.replace("../index.php?formulaire=Consultation_Sortie_St_Emb&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Emb=<?php echo $_POST['codeemb'];?>");
				</script>
                <?php
			}
			if	($_POST['typemouv']=='Entrée et Sortie')
			{
				?>
                <script language="javascript" type="text/javascript">
				window.location.replace("../index.php?formulaire=Consultation_Mouv_St_Emb&DateD=<?php echo $_POST["DateD"];?>&DateF=<?php echo $_POST["DateF"];?>&Emb=<?php echo $_POST['codeemb'];?>");
				</script>
                <?php
			}
}
	?>