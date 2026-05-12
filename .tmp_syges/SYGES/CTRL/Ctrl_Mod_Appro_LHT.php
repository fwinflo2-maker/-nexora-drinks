<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro']))
	{
		// mise à jour dans la bd
		$insere=0;
		$sql="update approvisionnement set date_appro=:date_appro, observation=:observationappro, liquideht=:liquideht, nbrecolis=:nbrecolis where id_appro='".$_POST['codeappro']."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'date_appro' =>  dateFormatAnglais($_POST['date_appro']),
										'observationappro' =>$_POST['observationappro'],
										'liquideht' =>$_POST['liquideht'],
										'nbrecolis' =>$_POST['nbrecolis']
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
				window.location.replace("../index.php?formulaire=Choisir_Appro_Mod_LHT");
				</script>
			<?php
			exit();
		}
}
?>
	