<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codevente']))
	{
		$id=htmlentities(htmlspecialchars(strtolower($_POST["codevente"])), ENT_QUOTES, 'UTF-8');
		//ici on verifie si la vente est en instance
		$valider=false;
		$sql4=" select statut from sortie_stock where id_sortiestock='".$id."'";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
			if ($rslt4['statut']=='N')
			{
				$valider=true;
			}
		}
		if ($valider==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Vente encore en instance.');
				history.back();
				</script>
			<?php
			exit();	
		}
//On recupere ts les emballages deconsigner a reduire du stock puis on verifie si la quantité sollicitee est en stock
		$trve8=false;
		$sql8 = " select e.id_emballage, e.qtestock, d.id_emballage, d.id_sortiestock, d.qte  from emballage e, rtrembvte d where d.id_sortiestock='".$_POST['codevente']."' and e.id_emballage=d.id_emballage";
		$reponse8= $DataBase->query($sql8);
		while($rslt8= $reponse8->fetch())
		{
			if ($rslt8['qtestock'] < $rslt8['qte'])
				{
					$trve8=true;
				}
		}
		if ($trve8==true)
		{
			?>
				<script language="javascript" type="text/javascript">
					alert('Stock des emballages non disponible.BV verifier les quantites a deduire.');
					history.back();
				</script>
			<?php
			exit();	
		}
		
			//ici on recupere tous les articles de la vente 
			$sql2=" select article.id_article, article.qtestock, articlevendu.id_article, articlevendu.qtesortie, articlevendu.id_sortiestock from article, articlevendu where articlevendu.id_sortiestock='".$id."' and article.id_article=articlevendu.id_article";
			$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
				//ici pour chaque enregistrement on fait la mise a jour du stock
				$sql9="update article set qtestock=:qtestock where id_article='".$rslt2["id_article"]."'";
				$req9 = $DataBase->prepare($sql9);
				$insere9 = $req9->execute(array(
											'qtestock' => $rslt2['qtestock']+$rslt2['qtesortie'],
											 ));	
			//ici on enregistre dans la liste des mouvements de stocks
			$operation='AN_VAL_VENTE';
			$sql="insert into mouvementar values (id_mouv,:codeoperation,:codeart,:date,:heure,:si,:qte,:sf,:operation,:user,:detenteur,:date_ann)";
			$req = $DataBase->prepare($sql);
			$insere2 = $req->execute(array(
										'codeoperation' =>$_POST['codevente'],
										'codeart' =>$rslt2["id_article"],
										'date' =>dateFormatAnglais($_POST['date_vente']),
										'heure' =>date('H:i'),
										'si' =>$rslt2['qtestock'],
										'qte' =>$rslt2['qtesortie'],
										'sf' =>$rslt2['qtestock']+$rslt2['qtesortie'],
										'operation' =>$operation,
										'user' =>$_SESSION['login'],
										'detenteur' =>$_POST['nomclient'],
										'date_ann' =>date('Y/m/d')

										));	
			
			if($insere9==0)
			{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la mise a jour des stocks des articles.');
				history.back();
				</script>
			<?php
			exit();	
			}
		}
			//ici on recupere toutes les consignes de la vente 
			$sql6=" select e.id_emballage, e.qtestock,e.qte as qteemb, c.id_emballage, c.qte, c.statut, c.id_sortiestock from emballage e, consigne c where c.id_sortiestock='".$id."' and e.id_emballage=c.id_emballage";
			$reponse6= $DataBase->query($sql6);
			while($rslt6= $reponse6->fetch())
			{
				//ici pour chaque enregistrement on fait la mise a jour du stock 
				$sql8="update emballage set qtestock=:qtestock where id_emballage='".$rslt6["id_emballage"]."'";
				$req8 = $DataBase->prepare($sql8);
				$insere8 = $req8->execute(array(
											'qtestock' => $rslt6['qtestock']+$rslt6['qte'],
											 ));	
			//ici on enregistre dans la liste des mouvements de stocks
			$operation='AN_VAL_CONSIGNE_CLIENT';
			$sql="insert into mouvementemb values (id_mouv,:codeoperation,:codeemb,:date,:heure,:qte,:siqte,:sfqte,:sistock,:sfstock,:operation,:user)";
			$req = $DataBase->prepare($sql);
			$insere7 = $req->execute(array(
										'codeoperation' =>$_POST['codevente'],
										'codeemb' =>$rslt6["id_emballage"],
										'date' =>dateFormatAnglais($_POST['date_vente']),
										'heure' =>date('H:i'),
										'qte' =>$rslt6['qte'],
										'siqte' =>$rslt6['qteemb'],
										'sfqte' =>$rslt6['qteemb'],
										'sistock' =>$rslt6['qtestock'],
										'sfstock' => $rslt6['qtestock']+$rslt6['qte'],
										'operation' =>$operation,
										'user' =>$_SESSION['login']
										));

				if($insere8==0)
				{
					?>
					<script language="javascript" type="text/javascript">
						alert('Echec de la mise a jour des stocks des emballages.');
						history.back();
					</script>
					<?php
				exit();	
				}
				//pour chaque consigne on fait la mise à jour des statuts 
				$stat='NV';
				$ins7=0;
				$sql7="update consigne set statut=:statut where id_emballage='".$rslt6["id_emballage"]."' and id_sortiestock='".$id."' ";
				$req7 = $DataBase->prepare($sql7);
				$ins7 = $req7->execute(array(
										'statut' => $stat,
										
										 ));
			if($ins7==0)
			{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la mise a jour des statuts des consignes.');
				history.back();
				</script>
			<?php
			exit();	
			}	
		}
		
//ici on recupere toutes les deconsignations de la vente
		$sql6=" select e.id_emballage, e.qtestock,e.qte as qteemb, d.id_emballage, d.qte, d.statut, d.id_sortiestock from emballage e, rtrembvte d where d.id_sortiestock='".$_POST['codevente']."' and e.id_emballage=d.id_emballage";
		$reponse6= $DataBase->query($sql6);
		while($rslt6= $reponse6->fetch())
		{
			//ici pour chaque enregistrement on fait la mise a jour du stock 
			$sql7="update emballage set qte=:qte, qtestock=:qtestock where id_emballage='".$rslt6["id_emballage"]."'";
			$req7 = $DataBase->prepare($sql7);
			$insere7 = $req7->execute(array(
										'qte' => $rslt6['qteemb']-$rslt6['qte'],
										'qtestock' => $rslt6['qtestock']-$rslt6['qte'],
										
										 ));	
			//ici on enregistre dans la liste des mouvements de stocks
			$operation='AN_VAL_DECONSIGNATION_EMB_VTE';
			$sql="insert into mouvementemb values (id_mouv,:codeoperation,:codeemb,:date,:heure,:qte,:siqte,:sfqte,:sistock,:sfstock,:operation,:user)";
			$req = $DataBase->prepare($sql);
			$insere7 = $req->execute(array(
										'codeoperation' =>$_POST['codevente'],
										'codeemb' =>$rslt6["id_emballage"],
										'date' =>dateFormatAnglais($_POST['date_vente']),
										'heure' =>date('H:i'),
										'qte' =>$rslt6['qte'],
										'siqte' =>$rslt6['qteemb'],
										'sfqte' =>$rslt6['qteemb']-$rslt6['qte'],
										'sistock' =>$rslt6['qtestock'],
										'sfstock' =>$rslt6['qtestock']-$rslt6['qte'],
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
			//pour chaque deconsignation on fait la mise à jour des statuts 
			$stat='NV';
			$sql7="update rtrembvte set statut=:statut where id_emballage='".$rslt6["id_emballage"]."' and id_sortiestock='".$_POST['codevente']."' ";
			$req7 = $DataBase->prepare($sql7);
			$ins7 = $req7->execute(array(
										'statut' => $stat,
										 ));	
			if($ins7==0)
			{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la mise à jour des statuts des deconsignations.');
				history.back();
				</script>
			<?php
			exit();	
			}
		}


	//ici on supprime le credit ristourne dans les charges si # de 0
		if ($_POST['ristourne']!=0)
		{
			$supp=0;
			$sql="delete from charge  where description like '%".$id."%' and id_typecharge='CREDIT_RISTOURNE'";
			$req = $DataBase->prepare($sql);
			$supp = $req->execute();	
			
			if($supp==0)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Echec de la suppression du Credit Ristourne.');
						history.back();
					</script>
				<?php
				exit();	
			}
		}
		// mise à jour dans la bd du statut de la vente
		$insere=0;
		$statut='N';
		$sql="update sortie_stock set login=:login, statut=:statut where id_sortiestock='".$id."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'login' =>$_SESSION['login'],
										'statut' => $statut
										 ));	
		if($insere==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la mise a jour.');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
		?>
				<script language="javascript" type="text/javascript">
				alert('Retour en instante de la vente reussie.');
				history.back();
				</script>
			<?php
			exit();
		}
	
	}

?>
	