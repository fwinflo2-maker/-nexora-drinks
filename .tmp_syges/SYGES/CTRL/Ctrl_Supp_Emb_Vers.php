<?php
	include("Connexion.php");
	include('../fonctions.php');
		//on verifie le statut de la vers
		$sql5 = " select statut from versement where num_vers='".$_POST["num_vers"]."'";
		$reponse5= $DataBase->query($sql5);
		$rslt5= $reponse5->fetch();
		if ($rslt5["statut"]=='V')
			{
					?>
						<script language="javascript" type="text/javascript">
							alert('Ce Versement est déjà validée. ');
							window.location.replace("../index.php?formulaire=Choisir_Vers_Mod");
						</script>
					<?php
					exit();	
			}

		// mise à jour dans la bd
	
		$sql="delete from emballage_vers where num_vers='".$_POST["num_vers"]."' and  id_emballage='".$_POST["EMB"]."'";
        $req = $DataBase->prepare($sql);
		$insere = $req->execute();	
		
		?>
        
				<script language="javascript" type="text/javascript">
				/*alert('Suppression effectue');*/
				window.location.replace("../index.php?formulaire=Modification_Vers&Vers=<?php echo $_POST["num_vers"];?>&VD=<?php echo $_POST["vendeur"];?>");
				</script>
			<?php
			exit();
?>