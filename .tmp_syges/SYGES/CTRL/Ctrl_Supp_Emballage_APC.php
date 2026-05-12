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
		//on verifie le statut de l'appro si c non valide alors pas de mise à jr du stock des emballages
		$sql3='SELECT STATUT FROM APPROCESSION WHERE ID_APPRO="'.$_POST['codeappro'].'" ' ;
		$reponse3= $DataBase->query($sql3);
		while($rslt3= $reponse3->fetch())
		{
			$statut = $rslt3['STATUT']; 
		}
		if ($statut=='V')
		{
			$sql2="update emballage set qtestock=:qtestock, qte=:qte where id_emballage='".$emb."'";
			$req2 = $DataBase->prepare($sql2);
			$insere2 = $req2->execute(array(
										'qtestock' =>$st+$_POST['qte'],
										'qte' =>$qt+$_POST['qte'],
										 ));
		}

		// mise à jour dans la bd
	
		$sql="delete from consignecession where id_consigne='".$_POST["codeconsigne"]."'";
        $req = $DataBase->prepare($sql);
		$insere = $req->execute();	
		?>
				<script language="javascript" type="text/javascript">
				/*alert('Suppression effectue');*/
				window.location.replace("../index.php?formulaire=Modification_ApproCession&Ap=<?php echo $_POST["codeappro"];?>");
				</script>
			<?php
			exit();
?>