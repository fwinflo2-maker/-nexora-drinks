<?php
	include("Connexion.php");
	include('../fonctions.php');
	    $sql='SELECT  ID_EMBALLAGE, QTESTOCK, QTE FROM EMBALLAGE WHERE LIBELLE="'.$_POST['emb'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt1= $reponse->fetch())
		{
			$emb = $rslt1['ID_EMBALLAGE']; 
			$st = $rslt1['QTESTOCK'];
			$qt = $rslt1['QTE'];
		}

		//on verifie le statut de la consigne si c deconsigne alors pas de mise à jr du stock
		$sql3='SELECT STATUT FROM CONSIGNEAPP WHERE ID_APPRO="'.$_POST['codeappro'].'" ' ;
		$reponse3= $DataBase->query($sql3);
		while($rslt3= $reponse3->fetch())
		{
			$statut = $rslt3['STATUT']; 
		}
		if ($statut=='Consigne')
		{
			$sql2="update emballage set qtestock=:qtestock, qte=:qte where id_emballage='".$emb."'";
			$req2 = $DataBase->prepare($sql2);
			$insere2 = $req2->execute(array(
										'qtestock' =>$st+$_POST['qte'],
										'qte' =>$qt+$_POST['qte'],
										 ));
		}

		// mise à jour dans la bd
	
		$sql="delete from consigneapp where id_consigne='".$_POST["codeconsigne"]."'";
        $req = $DataBase->prepare($sql);
		$insere = $req->execute();	
		//
	    $sql='SELECT  ID_FOURNISSEUR FROM APPROVISIONNEMENT WHERE ID_APPRO="'.$_POST['codeappro'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt2= $reponse->fetch())
		{
			$Fs = $rslt2['ID_FOURNISSEUR']; 
		}
		?>
        
				<script language="javascript" type="text/javascript">
				/*alert('Suppression effectue');*/
				window.location.replace("../index.php?formulaire=Enreg_Consigne_Appro&Ap=<?php echo $_POST["codeappro"];?>&Fs=<?php echo $Fs;?>");
				</script>
			<?php
			exit();
?>