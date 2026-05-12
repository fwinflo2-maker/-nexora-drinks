<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codecession']))
	{
		//on verifie le statut de la cession 
		$sql5 = " select statut from sortie_stock_cession where id_sortiestock='".$_POST["codecession"]."'";
		$reponse5= $DataBase->query($sql5);
		$rslt5= $reponse5->fetch();
		if ($rslt5["statut"]=='V')
		{
			?>
				<script language="javascript" type="text/javascript">
					alert('Cette Cession est déjà validée. ');
					window.location.replace("../index.php?formulaire=Choisir_SortieCession_Mod");
				</script>
			<?php
			exit();	
		}
		// mise à jour dans la bd
		$id=htmlentities(htmlspecialchars(strtolower($_POST["codecession"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$sql="update sortie_stock_cession set datesortiestock=:date, observation=:observation where id_sortiestock='".$id."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'date' =>  dateFormatAnglais($_POST['date']),
										'observation' =>$_POST['observation'],
										 ));	
		
		
		if($insere==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la mise à jour.');
					history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
		?>
				<script language="javascript" type="text/javascript">
				alert('Modification effectue');
				window.location.replace("../index.php?formulaire=Modification_SortieCession&SC=<?php echo $_POST["codecession"]; ?>");
				</script>
			<?php
			exit();
		}
}
?>
	