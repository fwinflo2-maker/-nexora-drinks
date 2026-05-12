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
			 //ON RECUPERE LE CODE ART ET LE NBRE DE BTE
	    	$sql='SELECT  ID_ARTICLE, NBREBTE FROM ARTICLE WHERE LIBELLE="'.$_POST['codeart'].'" ' ;
			$reponse= $DataBase->query($sql);
			while($rslt1= $reponse->fetch())
			{
				$art = $rslt1['ID_ARTICLE']; 
				$bte = $rslt1['NBREBTE']; 
			}

			// mise à jour dans la bd
			$insere=0;
			$sql="update article_recu_Frigo set qterecu=:qterecu,nbrebte=:nbrebte where id_article='".$art."' and  id_appro='".$appro."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'qterecu' =>$_POST['qterecu'],
										'nbrebte' =>($_POST['qterecu']*$bte)
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
					/*alert('Modification effectue');*/
					window.location.replace("../index.php?formulaire=Modification_Appro_Frigo&Ap=<?php echo $_POST["codeappro"];?>");
				</script>
			<?php
			exit();
		}
	}
?>