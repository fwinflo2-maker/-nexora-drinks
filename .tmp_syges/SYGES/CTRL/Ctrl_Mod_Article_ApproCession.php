<?php
	include("Connexion.php");
	include('../fonctions.php');
		// ON VERIFIE  SI L'APPRO EST VALIDEE
		$sql2='SELECT STATUT FROM APPROCESSION WHERE ID_APPRO="'.$_POST['codeappro'].'" ' ;
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
	
			// mise à jour dans la bd
			$appro=htmlentities(htmlspecialchars(strtolower($_POST["codeappro"])), ENT_QUOTES, 'UTF-8');
			$insere=0;
			$sql="update article_recu_cession set qterecu=:qterecu where id_article='".$art."' and  id_appro='".$appro."'";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
											'qterecu' =>$_POST['qterecu']
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
			$sql='SELECT  * FROM APPROCESSION WHERE ID_APPRO="'.$_POST['codeappro'].'" ' ;
			$reponse= $DataBase->query($sql);
			while($rslt1= $reponse->fetch())
			{
				$Fssr = $rslt1['LOGIN']; 
			}
			?>
			
			<script language="javascript" type="text/javascript">
				/*alert('Modification effectue');*/
				window.location.replace("../index.php?formulaire=Modification_ApproCession&Ap=<?php echo $_POST["codeappro"];?>");
			</script>
			<?php
			exit();
		}
	}
?>