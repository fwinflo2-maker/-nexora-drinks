<?php
	include("Connexion.php");
	include("../fonctions.php");
    session_start();
		//ici on verifie si le versement est deja prise en compte 
		$valider=false;
		$sql4=" select statut from versement where num_vers='".$_POST['num_vers']."'";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
			if ($rslt4['statut']=='V')
			{
				$valider=true;
			}
		}
		if ($valider==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Versement deja valide.');
				history.back();
				</script>
			<?php
			exit();	
		}

		// mise à jour dans la bd
		$code=htmlentities(htmlspecialchars(strtolower($_POST["num_vers"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$sql="update versement set date_vers=:date_vers, vendeur=:vendeur, observation=:observation,montant=:montant, user=:user, date=:date,heure=:heure where num_vers='".$code."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'date_vers' =>dateFormatAnglais($_POST["date"]),
										'vendeur' =>$_POST["vendeur"],
										'montant' =>$_POST["Montant"],
										'observation' =>$_POST["observation"],
										'user' =>$_SESSION['login'],
										'date' =>date('Y-m-d'),
										'heure' =>date('H:i')
										));	
			
		if($insere==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la mise à jour du versement.');
					history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
		?>
				<script language="javascript" type="text/javascript">
				alert('MODIFICATION EFFECTUEE!');
				window.location.replace("../index.php?formulaire=Modification_Vers&Vers=<?php echo $code; ?>&VD=<?php echo $_POST["vendeur"]; ?>");
				</script>
			<?php
			exit();
		}
?>