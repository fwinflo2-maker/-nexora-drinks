<?php
	include("Connexion.php");
	include('../fonctions.php');

		// mise à jour dans la bd
$id=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$sql="update fournisseur set nom=:nom, numtel=:numtel, email=:email, statut=:statut where id_fournisseur='".$id."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'nom' =>$_POST['nom'],
										'numtel' =>$_POST['numtel'],
										'email' =>$_POST['email'],
										'statut' => $_POST['statut']
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
				window.location.replace("../index.php?formulaire=Choisir_Fournisseur_Mod");
				</script>
			<?php
			exit();
		}
?>