<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['num_vers']))
	{
		//ici on verifie si le versement existe dans la base de donnee
		$supp=false;
		$sql4=" select * from versement where num_vers='".$_POST['num_vers']."'";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
			$supp=true;
		}
		if ($supp==true)
		{
			$id=htmlentities(htmlspecialchars(strtolower($_POST["num_vers"])), ENT_QUOTES, 'UTF-8');
	
			//ici on recupere et supprime les emballages du  versement

			$sql6="delete from emballage_vers where num_vers='".$id."'";
			$req6 = $DataBase->prepare($sql6);
			$insere6 = $req6->execute();
			
			// suppression dans la bd
			$sql="delete from versement where num_vers='".$id."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute();
?>
				<script language="javascript" type="text/javascript">
				alert('Suppression effectue');
				window.location.replace("../index.php?formulaire=Choisir_Vers_Supp");
				</script>
				<?php
				exit();
        }
		else
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Versement deja supprime.');
				history.back();
				</script>
			<?php
			exit();	
		}
	}
?>
	