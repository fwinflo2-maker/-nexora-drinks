<?php
	include("Connexion.php");

		// mise à jour dans la bd
$login=htmlentities(htmlspecialchars(strtolower($_POST["Login"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$sql="update user set nom=:nom, prenom=:prenom, habilitation=:habilitation, mdp=:mdp, statut=:statut where login='".$login."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'nom' =>$_POST['nom'],
										'prenom' =>$_POST['prenom'],
										'habilitation' =>$_POST['Habilitation'],
										'mdp' =>$_POST['MDP'],
										'statut' =>$_POST['statut']
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
				window.location.replace("../index.php?formulaire=Choisir_Utilisateur_Mod_Gerant");
				</script>
			<?php
			exit();
		}
?>