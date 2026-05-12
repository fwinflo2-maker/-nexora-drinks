<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro']))
	{
		//ici on verifie si l'appro est deja  valide
		$valider=false;
		$sql4=" select statut from approcession where id_appro='".$_POST['codeappro']."'";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
			if ($rslt4['statut']=='V')
			{
				$valider=true;
			}
		}
		if ($valider==true)
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
			// mise à jour dans la bd
			$id=htmlentities(htmlspecialchars(strtolower($_POST["codeappro"])), ENT_QUOTES, 'UTF-8');
			$insere=0;
			$statut='V';
			$sql="update approcession set statut=:statut where id_appro='".$id."'";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
											'statut' => $statut,
											 ));	
			//ici on recupere tous les articles de l'appro 
			$sql2=" select article.id_article, article.qtestock, article_recu_cession.id_article, article_recu_cession.qterecu, article_recu_cession.id_appro from article, article_recu_cession where article_recu_cession.id_appro='".$id."' and article.id_article=article_recu_cession.id_article";
			$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
				//ici pour chaque enregistrement on fait la mise a jour du stock
				$sql="update article set qtestock=:qtestock where id_article='".$rslt2["id_article"]."'";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
											'qtestock' => $rslt2['qterecu']+$rslt2['qtestock'],
											 ));	
				//ici on enregistre dans la liste des mouvements de stocks
				$operation='APPRO_CESSION';
				$sql="insert into mouvementar values (id_mouv,:codeoperation,:codeart,:date,:heure,:si,:qte,:sf,:operation,:user,:detenteur,:date_ann)";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
										'codeoperation' =>$_POST['codeappro'],
										'codeart' =>$rslt2["id_article"],
										'date' =>dateFormatAnglais($_POST['date_appro']),
										'heure' =>date('H:i'),
										'si' =>$rslt2['qtestock'],
										'qte' =>$rslt2['qterecu'],
										'sf' =>$rslt2['qterecu']+$rslt2['qtestock'],
										'operation' =>$operation,
										'user' =>$_SESSION['login'],
										'detenteur' =>$operation,
										'date_ann' =>date('Y/m/d')
										));	
			
					
			if($insere==0)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Echec de la mise à jour du stock des articles.');
					history.back();
					</script>
				<?php
				exit();	
			}
			}
			//ici on recupere toutes les consignes de l'appro cession 
			$sql6=" select e.id_emballage, e.qtestock,e.qte as qteemb, c.id_emballage, c.qte, c.id_appro from emballage e, consignecession c where c.id_appro='".$_POST['codeappro']."' and e.id_emballage=c.id_emballage";
			$reponse6= $DataBase->query($sql6);
			while($rslt6= $reponse6->fetch())
			{
				//ici pour chaque enregistrement on fait la mise a jour du stock 
				$sql7="update emballage set qte=:qte, qtestock=:qtestock where id_emballage='".$rslt6["id_emballage"]."'";
				$req7 = $DataBase->prepare($sql7);
				$insere7 = $req7->execute(array(
											'qte' => $rslt6['qteemb']+$rslt6['qte'],
											'qtestock' => $rslt6['qtestock']+$rslt6['qte'],
											 ));	
			//ici on enregistre dans la liste des mouvements de stocks
			$operation='CONSIGNE_EMB_APPRO_CESSION';
			$sql="insert into mouvementemb values (id_mouv,:codeoperation,:codeemb,:date,:heure,:qte,:siqte,:sfqte,:sistock,:sfstock,:operation,:user)";
			$req = $DataBase->prepare($sql);
			$insere7 = $req->execute(array(
										'codeoperation' =>$_POST['codeappro'],
										'codeemb' =>$rslt6["id_emballage"],
										'date' =>dateFormatAnglais($_POST['date_appro']),
										'heure' =>date('H:i'),
										'qte' =>$rslt6['qte'],
										'siqte' =>$rslt6['qteemb'],
										'sfqte' =>$rslt6['qteemb']+$rslt6['qte'],
										'sistock' =>$rslt6['qtestock'],
										'sfstock' =>$rslt6['qtestock']+$rslt6['qte'],
										'operation' =>$operation,
										'user' =>$_SESSION['login']
										));
			
			if($insere7==0)
				{
					?>
					<script language="javascript" type="text/javascript">
						alert('Echec de la mise à jour des stocks des emballages.');
						history.back();
					</script>
					<?php
					exit();	
				}
			}

				?>
				<script language="javascript" type="text/javascript">
				alert('Approvisionnement pris en compte');
				window.location.replace("../Recu_ApproCession.php?Id=<?php echo $id;?>");
				</script>
				<?php
				exit();
	}
}
?>
	