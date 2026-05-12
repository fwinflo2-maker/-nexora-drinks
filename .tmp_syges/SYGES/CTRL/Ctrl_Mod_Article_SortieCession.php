<?php
	include("Connexion.php");
	include('../fonctions.php');
		//on verifie le statut de la cession 
		$sql5 = " select statut from sortie_stock_cession where id_sortiestock='".$_POST["codecession"]."'";
		$reponse5= $DataBase->query($sql5);
		$rslt5= $reponse5->fetch();
		if ($rslt5["statut"]=='V')
		{
			?>
				<script language="javascript" type="text/javascript">
					alert('Cette Cession est déjà validée. ');
					window.location.replace("../index.php?formulaire=Choisir_SortieCession_Mod");
				</script>
			<?php
			exit();	
		}
	    $sql='SELECT  ID_ARTICLE FROM ARTICLE WHERE LIBELLE="'.$_POST['codeart'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt1= $reponse->fetch())
		{
			$art = $rslt1['ID_ARTICLE']; 
		}

		// mise à jour dans la bd
		$cession=htmlentities(htmlspecialchars(strtolower($_POST["codecession"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$sql="update articlesortie_cession set qtesortie=:qtesortie where id_article='".$art."' and  id_sortiestock='".$cession."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'qtesortie' =>$_POST['qtesortie']
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
				window.location.replace("../index.php?formulaire=Modification_SortieCession&SC=<?php echo $_POST["codecession"];?>");
				</script>
			<?php
			exit();
		}
?>