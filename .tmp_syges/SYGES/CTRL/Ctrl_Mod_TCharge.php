<?php
	include("Connexion.php");

		// mise à jour dans la bd
$code=htmlentities(htmlspecialchars(strtolower($_POST["Code"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$sql="update type_charge set id_typecharge=:id_typecharge, libelle=:libelle, statut=:statut where id_typecharge='".$code."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'id_typecharge' =>$_POST['Code'],
										'libelle' =>$_POST['Libelle'],
										'statut' =>$_POST['Statut'],
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
				window.location.replace("../index.php?formulaire=Choisir_TCharge_Mod");
				</script>
			<?php
			exit();
		}
?>