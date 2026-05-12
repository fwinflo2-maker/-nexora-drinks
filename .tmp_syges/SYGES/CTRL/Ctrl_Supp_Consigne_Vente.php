<?php
	include("Connexion.php");
	include('../fonctions.php');
	    $sql='SELECT  ID_EMBALLAGE, QTESTOCK, QTE FROM EMBALLAGE WHERE LIBELLE="'.$_POST['emb'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt1= $reponse->fetch())
		{
			$emb = $rslt1['ID_EMBALLAGE']; 
			$st = $rslt1['QTESTOCK'];
			$qte = $rslt1['QTE'];
		}
		//on verifie le staut de la consigne si c deconsigne alors pas de mise à jr du stock
		$sql3='SELECT STATUT FROM CONSIGNE WHERE ID_SORTIESTOCK="'.$_POST['codevente'].'" ' ;
		$reponse3= $DataBase->query($sql3);
		while($rslt3= $reponse3->fetch())
		{
			$statut = $rslt3['STATUT']; 
		}
		if ($statut=='Consigne')
		{
			$sql2="update emballage set qtestock=:qtestock where id_emballage='".$emb."'";
			$req2 = $DataBase->prepare($sql2);
			$insere2 = $req2->execute(array(
										'qtestock' =>$st+$_POST['qte'],
										 ));
			//ici on enregistre dans la liste des mouvements de stocks
			$operation='SUPP_CONSIGNE_VENTE';
			$sql="insert into mouvementemb values (id_mouv,:codeoperation,:codeemb,:date,:heure,:qte,:siqte,:sfqte,:sistock,:sfstock,:operation,:user)";
			$req = $DataBase->prepare($sql);
			$insere4 = $req->execute(array(
												'codeoperation' =>$_POST['codevente'],
												'codeemb' =>$emb,
												'date' => dateFormatAnglais(date('d/m/Y')),
												'heure' =>date('H:i'),
												'qte' =>$_POST['qte'],
												'siqte' =>$qte,
												'sfqte' =>$qte,
												'sistock' =>$st,
												'sfstock' => $st+$_POST['qte'],
												'operation' =>$operation,
												'user' =>$_SESSION['login']
												));
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
				window.location.replace("../index.php?formulaire=Enreg_Consigne&Vte=<?php echo $_POST["codevente"];?>&Clt=<?php echo $Clt;?>");
				</script>
			<?php
			exit();
?>