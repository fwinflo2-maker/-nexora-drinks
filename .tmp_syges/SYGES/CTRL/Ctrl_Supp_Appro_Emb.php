<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro']))
	{
		//ici on verifie si l'appro existe dans la base de donnee
		$supp=false;
		$sql4=" select * from approemb where id_appro='".$_POST['codeappro']."'";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
			$supp=true;
		}
		if ($supp==true)
		{
			$id=htmlentities(htmlspecialchars(strtolower($_POST["codeappro"])), ENT_QUOTES, 'UTF-8');

			//ici on recupere et supprime les emballages de cet appro
		
			$sql2='select id_emballage, id_appro from emballage_recu where id_appro="'.$id.'" ' ;
			$reponse= $DataBase->query($sql2);
			while($rslt= $reponse->fetch())
			{
				$sql3="delete from emballage_recu where id_appro='".$id."' and id_emballage='".$rslt['id_emballage']."'";
				$req2 = $DataBase->prepare($sql3);
				$insere2 = $req2->execute();
			}
			// suppression dans la bd
			$sql="delete from approemb where id_appro='".$id."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute();	
?>
				<script language="javascript" type="text/javascript">
				alert('Suppression effectue');
				window.location.replace("../index.php?formulaire=Choisir_Appro_Emb_Supp");
				</script>
				<?php
				exit();
        }
		else
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Appro deja supprime.');
				history.back();
				</script>
			<?php
			exit();	
		}
	}
?>
	