<?php
	include("Connexion.php");
	include('../fonctions.php');
	    $sql='SELECT  ID_EMBALLAGE, QTESTOCK FROM EMBALLAGE WHERE LIBELLE="'.$_POST['emb'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt1= $reponse->fetch())
		{
			$emb = $rslt1['ID_EMBALLAGE']; 
			$st = $rslt1['QTESTOCK'];
		}

		// mise à jour dans la bd
	
		$sql="delete from consigne where id_emballage='".$emb."' and  id_sortiestock='".$_POST["codevente"]."'";
        $req = $DataBase->prepare($sql);
		$insere = $req->execute();	
		//
	    $sql='SELECT  ID_CLIENT FROM SORTIE_STOCK WHERE ID_SORTIESTOCK="'.$_POST['codevente'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt2= $reponse->fetch())
		{
			$Clt = $rslt2['ID_CLIENT']; 
		}
		?>
        
				<script language="javascript" type="text/javascript">
				/*alert('Suppression effectue');*/
				window.location.replace("../index.php?formulaire=Modification_Vente&Vte=<?php echo $_POST["codevente"];?>&Clt=<?php echo $Clt;?>");
				</script>
			<?php
			exit();
?>