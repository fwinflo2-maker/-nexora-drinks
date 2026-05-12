<?php
	include("Connexion.php");
	include('../fonctions.php');
	$appro=htmlentities(htmlspecialchars(strtolower($_POST['codeappro'])), ENT_QUOTES, 'UTF-8');
	// ON VERIFIE  SI L'APPRO EST VALIDEE
		$sql2='SELECT STATUT FROM APPROFRIGO WHERE ID_APPRO="'.$appro.'" ' ;
		$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{ 
			$statut=$rslt2['STATUT'];
		}
		if ($statut=='V')
		 {
			?>
				<script language="javascript" type="text/javascript">
				alert('Appro deja Validé.');
				history.back();
				</script>
			<?php
			exit(); 
		 }
		 else
		 {
	    	$sql='SELECT  ID_ARTICLE FROM ARTICLE WHERE LIBELLE="'.$_POST['codeart'].'" ' ;
			$reponse= $DataBase->query($sql);
			while($rslt1= $reponse->fetch())
			{
				$art = $rslt1['ID_ARTICLE']; 
			}

			// suppression dans la bd
			$sql="DELETE FROM ARTICLE_RECU_FRIGO WHERE ID_ARTICLE='".$art."' AND ID_APPRO='".$_POST['codeappro']."' ";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute();	

			?>
				<script language="javascript" type="text/javascript">
					/*alert('Suppression effectue');*/
					window.location.replace("../index.php?formulaire=Modification_Appro_Frigo&Ap=<?php echo $_POST["codeappro"];?>");
				</script>
			<?php
			exit();
		}
			?>