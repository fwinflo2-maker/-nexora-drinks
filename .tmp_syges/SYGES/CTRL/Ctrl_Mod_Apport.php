<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['Code']))
	{
		// on verifie si cet n'est pas validée  
		$id=htmlentities(htmlspecialchars(strtolower($_POST["Code"])), ENT_QUOTES, 'UTF-8');
		$sql5 = " select statut from apport where id_apport='".$id."'";
		$reponse5= $DataBase->query($sql5);
		$rslt5= $reponse5->fetch();
		if ($rslt5["statut"]=='V')
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cet apport est déjà validée. ');
					window.location.replace("../index.php?formulaire=Choisir_Apport_Mod");
					</script>
				<?php
				exit();	
			}
		// mise à jour dans la bd
		$code=htmlentities(htmlspecialchars(strtolower($_POST["Code"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$sql="update apport set  montant=:montant, date_apport=:date, libelle=:libelle where id_apport='".$code."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'montant' =>$_POST['Montant'],
										'date' =>dateFormatAnglais($_POST['Date']),
										'libelle' =>$_POST['Libelle'],
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
				window.location.replace("../index.php?formulaire=Choisir_Apport_Mod");
				</script>
			<?php
			exit();
		}
	}
?>