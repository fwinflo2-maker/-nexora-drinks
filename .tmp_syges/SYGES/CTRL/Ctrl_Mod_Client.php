<?php
	include("Connexion.php");
	include('../fonctions.php');

		// mise à jour dans la bd
		$id=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$sql="update client set nom=:nom, numtel=:numtel,niu=:niu,rc=:rc, email=:email, statut=:statut, id_categorie=:id_categorie,fraisenlevement=:fraisenlevement, fraisenlevement_pet=:fraisenlevement_pet, tauxristourneht=:tauxristourneht, psaristournes=:psaristournes where id_client='".$id."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'nom' =>$_POST['nom'],
										'numtel' =>$_POST['numtel'],
										'email' =>$_POST['email'],
										'niu' =>$_POST['niu'],
										'rc' =>$_POST['rc'],
										'id_categorie' =>$_POST['categorie'],
										'fraisenlevement' =>$_POST['fraisenlevement'],
										'fraisenlevement_pet' =>$_POST['fraisenlevement_pet'],
										'tauxristourneht' =>$_POST['tauxristourneht'],
										'psaristournes' =>$_POST['psaristournes'],
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
				window.location.replace("../index.php?formulaire=Choisir_Client_Mod");
				</script>
			<?php
			exit();
		}
?>