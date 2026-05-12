<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['Code']))
	{
		// on verifie si cette charge n'est pas en instance  
		$id=htmlentities(htmlspecialchars(strtolower($_POST["Code"])), ENT_QUOTES, 'UTF-8');
		$sql5 = " select statut from charge where id_charge='".$id."'";
		$reponse5= $DataBase->query($sql5);
		$rslt5= $reponse5->fetch();
		if ($rslt5["statut"]=='N')
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cette charge est en instance. ');
					window.location.replace("../index.php?formulaire=Choisir_Charge_An_Val");
					</script>
				<?php
				exit();	
			}
		// mise à jour dans la bd
		$code=htmlentities(htmlspecialchars(strtolower($_POST["Code"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$statut='N';
		$sql="update charge set statut=:statut where id_charge='".$code."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'statut' =>$statut,
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
				alert('Remise en instance de la charge effectuée.');
				window.location.replace("../index.php?formulaire=Choisir_Charge_An_Val");
				</script>
			<?php
			exit();
		}
	}
?>