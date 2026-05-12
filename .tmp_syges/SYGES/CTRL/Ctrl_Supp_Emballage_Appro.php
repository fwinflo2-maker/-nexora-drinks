<?php
	include("Connexion.php");
	include('../fonctions.php');
	//Ici on recupere le code de l'emballage
	    $sql='SELECT  ID_EMBALLAGE, STATUT FROM EMBALLAGE WHERE LIBELLE="'.$_POST['emb'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt1= $reponse->fetch())
		{
			$emb = $rslt1['ID_EMBALLAGE']; 
		}
	// ON VERIFIE  SI L'APPRO EST VALIDEE
		$sql2='SELECT STATUT FROM APPROEMB WHERE ID_APPRO="'.$_POST['codeappro'].'" ' ;
		$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{ 
			$staut=$rslt2['STATUT'];
		}
		if ($staut=='V')
		 {
			?>
				<script language="javascript" type="text/javascript">
				alert('Appro deja pris en compte.');
				history.back();
				</script>
			<?php
			exit(); 
		 }
		 else
		 {
		// suppression dans la bd
		$sql="DELETE FROM EMBALLAGE_RECU WHERE ID_EMBALLAGE='".$emb."' AND ID_APPRO='".$_POST['codeappro']."' ";
		$req = $DataBase->prepare($sql);
		$insere = $req->execute();	
		?>
        
				<script language="javascript" type="text/javascript">
				/*alert('Suppression effectue');*/
				window.location.replace("../index.php?formulaire=Modification_Appro_Emb&Ap=<?php echo $_POST["codeappro"];?>");
				</script>
			<?php
			exit();
		}
?>