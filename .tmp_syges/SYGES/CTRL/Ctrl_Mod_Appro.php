<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro']))
	{
		//on se rassure de ce que la l'appro n'est pas validée
			$id=htmlentities(htmlspecialchars(strtolower($_POST['codeappro'])), ENT_QUOTES, 'UTF-8');
			$sql5 = " select statut from approvisionnement where id_appro='".$id."'";
			$reponse5= $DataBase->query($sql5);
			$rslt5= $reponse5->fetch();
			if ($rslt5["statut"]=='V')
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cet Appro est deja validee. ');
					window.location.replace("../index.php?formulaire=Choisir_Appro_Mod");
					</script>
				<?php
				exit();	
			}
		// mise à jour dans la bd
		$insere=0;
		$sql="update approvisionnement set date_appro=:date_appro, observation=:observationappro, liquideht=:liquideht, nbrecolis=:nbrecolis, id_fournisseur=:id_fournisseur where id_appro='".$id."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'date_appro' =>  dateFormatAnglais($_POST['date_appro']),
										'observationappro' =>$_POST['observationappro'],
										'liquideht' =>$_POST['liquideht'],
										'nbrecolis' =>$_POST['nbrecolis'],
										'id_fournisseur' =>$_POST['codefournisseur']
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
				window.location.replace("../index.php?formulaire=Modification_Appro&Ap=<?php echo $_POST["codeappro"]; ?>");
				</script>
			<?php
			exit();
		}
}
?>
	