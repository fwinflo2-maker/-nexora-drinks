<?php
	include("Connexion.php");

		// mise à jour dans la bd
		$code=htmlentities(htmlspecialchars(strtolower($_POST["Code"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$statut="V";
		$sql="update charge set statut=:statut where id_charge='".$code."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										
										'statut' =>$statut
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
				alert('Validation effectuee');
				window.location.replace("../index.php?formulaire=Choisir_Charge_Mod");
				</script>
			<?php
			exit();
		}
?>