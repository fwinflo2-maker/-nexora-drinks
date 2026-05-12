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

		// suppression dans la bd
		$sql="DELETE FROM ARTICLESORTIE_CESSION WHERE ID_ARTICLE='".$art."' AND ID_SORTIESTOCK='".$_POST['codecession']."' ";
		$req = $DataBase->prepare($sql);
		$insere = $req->execute();	

		?>
        
				<script language="javascript" type="text/javascript">
				/*alert('Suppression effectue');*/
				window.location.replace("../index.php?formulaire=Modification_SortieCession&SC=<?php echo $_POST["codecession"];?>");
				</script>
			<?php
			exit();
?>