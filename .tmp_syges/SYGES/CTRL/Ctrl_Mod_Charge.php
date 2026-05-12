<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['Code']))
	{
		// on verifie si cette charge n'est pas validée  
		$id=htmlentities(htmlspecialchars(strtolower($_POST["Code"])), ENT_QUOTES, 'UTF-8');
		$sql5 = " select statut from charge where id_charge='".$id."'";
		$reponse5= $DataBase->query($sql5);
		$rslt5= $reponse5->fetch();
		if ($rslt5["statut"]=='V')
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cette charge est déjà validée. ');
					window.location.replace("../index.php?formulaire=Choisir_Charge_Mod");
					</script>
				<?php
				exit();	
			}
		// mise à jour dans la bd
		$code=htmlentities(htmlspecialchars(strtolower($_POST["Code"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$sql="update charge set id_charge=:id_charge, id_typecharge=:id_typecharge, montant=:montant, date_charge=:datecharge, description=:description where id_charge='".$code."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'id_charge' =>$_POST['Code'],
										'id_typecharge' =>$_POST['Typecharge'],
										'montant' =>$_POST['Montant'],
										'datecharge' =>dateFormatAnglais($_POST['Date']),
										'description' =>$_POST['Description'],
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
				window.location.replace("../index.php?formulaire=Choisir_Charge_Mod");
				</script>
			<?php
			exit();
		}
	}
?>