<?php
	include("Connexion.php");
    session_start();
		//ici on verifie si le versement est deja prise en compte 
		$valider=false;
		$sql4=" select statut from versement where num_vers='".$_POST['num_vers']."'";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
			if ($rslt4['statut']=='N')
			{
				$valider=true;
			}
		}
		if ($valider==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('VERSEMENT ENCORE EN INSTANCE.');
				history.back();
				</script>
			<?php
			exit();	
		}

		// mise à jour dans la bd
		$code=htmlentities(htmlspecialchars(strtolower($_POST["num_vers"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$statut="N";
		$sql="update versement set statut=:statut, user=:user, date=:date,heure=:heure where num_vers='".$code."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										
										'statut' =>$statut,
										'user' =>$_SESSION['login'],
										'date' =>date('Y-m-d'),
										'heure' =>date('H:i')
										));	
			
		if($insere==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la mise à jour du statut du versement.');
					history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
		?>
				<script language="javascript" type="text/javascript">
				alert('VERSEMENT ANNULE.');
				window.location.replace("../index.php?formulaire=Choisir_Vers_Mod");
				</script>
			<?php
			exit();
		}
?>