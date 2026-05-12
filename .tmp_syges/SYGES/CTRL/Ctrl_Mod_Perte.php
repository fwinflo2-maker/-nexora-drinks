<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codevente']))
	{
		//on se rassure de ce que la vente n'est pas validée
			$id=htmlentities(htmlspecialchars(strtolower($_POST["codevente"])), ENT_QUOTES, 'UTF-8');
			$sql5 = " select statut from sortie_stock_frigo where id_sortiestock='".$id."'";
			$reponse5= $DataBase->query($sql5);
			$rslt5= $reponse5->fetch();
			if ($rslt5["statut"]=='V')
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cette vente est déjà validée. ');
					window.location.replace("../index.php?formulaire=Choisir_Perte_Mod");
					</script>
				<?php
				exit();	
			}
		// mise à jour dans la bd
		$insere=0;
		$sql="update sortie_stock_frigo set datesortiestock=:date_vente, observation=:observationsortie , login=:login where id_sortiestock='".$id."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'date_vente' =>  dateFormatAnglais($_POST['date_vente']),
										'observationsortie' =>$_POST['observationsortie'],
										'login' =>$_SESSION['login']
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
				window.location.replace("../index.php?formulaire=Modification_Perte&Vte=<?php echo $_POST["codevente"]; ?>");
				</script>
			<?php
			exit();
		}
}
?>
	