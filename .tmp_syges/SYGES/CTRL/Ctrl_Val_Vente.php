<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codevente']))
	{
		
		//ici on verifie si la vente est deja prise en compte 
		$valider=false;
		$sql4=" select statut, creditristourne, id_client from sortie_stock where id_sortiestock='".$_POST['codevente']."'";
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
				alert('Vente deja prise en compte.');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
		
		//ici on recupere tous les articles de la vente on verifie la quantite en stock
		$id=htmlentities(htmlspecialchars(strtolower($_POST["codevente"])), ENT_QUOTES, 'UTF-8');
		$trve3=false;
		$sql3=" select article.id_article, article.qtestock, articlevendu.id_article, articlevendu.qtesortie, articlevendu.id_sortiestock from article, articlevendu where articlevendu.id_sortiestock='".$id."' and article.id_article=articlevendu.id_article";
		$reponse3= $DataBase->query($sql3);
		while($rslt3= $reponse3->fetch())
		{
			if ($rslt3['qtestock'] < $rslt3['qtesortie'])
				{
					$trve3=true;
			
				}
		}
		if ($trve3==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Stock des articles non disponible.BV verifier les quantites a sortir');
				history.back();
				</script>
			<?php
			exit();	
		}
		//On recupere ts les emballages de la vente puis on verifie si la quantité sollicitee est en stock
		$trve5=false;
		$sql5 = " select e.id_emballage, e.qtestock, c.id_emballage, c.id_sortiestock, c.qte  from emballage e, consigne c where c.id_sortiestock='".$id."' and e.id_emballage=c.id_emballage";
		$reponse5= $DataBase->query($sql5);
		while($rslt5= $reponse5->fetch())
		{
			if ($rslt5['qtestock'] < $rslt5['qte'])
				{
					$trve5=true;
				}
		}
		if ($trve5==true)
		{
			?>
				<script language="javascript" type="text/javascript">
					alert('Stock des emballages non disponible.BV verifier les quantitès à consigner');
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
			$sql="update article set qtestock=:qtestock where id_article='".$rslt2["id_article"]."'";
			$req = $DataBase->prepare($sql);
			$insere2 = $req->execute(array(
										'qtestock' => $rslt2['qtestock']-$rslt2['qtesortie'],
										 ));	
			//ici on enregistre dans la liste des mouvements de stocks
			$operation='VENTE';
			$sql="insert into mouvementar values (id_mouv,:codeoperation,:codeart,:date,:heure,:si,:qte,:sf,:operation,:user,:detenteur,:date_ann)";
			$req = $DataBase->prepare($sql);
			$insere2 = $req->execute(array(
										'codeoperation' =>$_POST['codevente'],
										'codeart' =>$rslt2["id_article"],
										'date' =>dateFormatAnglais($_POST['date_vente']),
										'heure' =>date('H:i'),
										'si' =>$rslt2['qtestock'],
										'qte' =>$rslt2['qtesortie'],
										'sf' =>$rslt2['qtestock']-$rslt2['qtesortie'],
										'operation' =>$operation,
										'user' =>$_SESSION['login'],
										'detenteur' =>$_POST['nomclient'],
										'date_ann' =>date('Y/m/d')
										));	
		if($insere2==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la mise à jour des stocks des articles.');
				history.back();
				</script>
			<?php
			exit();	
		}
		}
		//
		
//ici on recupere toutes les deconsignations de la vente
		$sql6=" select e.id_emballage, e.qtestock,e.qte as qteemb, d.id_emballage, d.qte, d.statut, d.id_sortiestock from emballage e, rtrembvte d where d.id_sortiestock='".$_POST['codevente']."' and e.id_emballage=d.id_emballage";
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
			$operation='DECONSIGNATION_EMB_VTE';
			$sql="insert into mouvementemb values (id_mouv,:codeoperation,:codeemb,:date,:heure,:qte,:siqte,:sfqte,:sistock,:sfstock,:operation,:user)";
			$req = $DataBase->prepare($sql);
			$insere7 = $req->execute(array(
										'codeoperation' =>$_POST['codevente'],
										'codeemb' =>$rslt6["id_emballage"],
										'date' =>dateFormatAnglais($_POST['date_vente']),
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
			//pour chaque deconsignation on fait la mise à jour des statuts 
			$stat='V';
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


		//ici on recupere toutes les consignes de la vente 
		$sql6=" select e.id_emballage, e.qtestock,e.qte as qteemb, c.id_emballage, c.qte, c.statut, c.id_sortiestock from emballage e, consigne c where c.id_sortiestock='".$id."' and e.id_emballage=c.id_emballage";
		$reponse6= $DataBase->query($sql6);
		while($rslt6= $reponse6->fetch())
		{
			//ici pour chaque enregistrement on fait la mise a jour du stock 
			$sql6="update emballage set qtestock=:qtestock where id_emballage='".$rslt6["id_emballage"]."'";
			$req6 = $DataBase->prepare($sql6);
			$insere6 = $req6->execute(array(
										'qtestock' => $rslt6['qtestock']-$rslt6['qte'],
										 ));	
			//ici on enregistre dans la liste des mouvements de stocks
			$operation='CONSIGNE_CLIENT';
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
										'sfstock' => $rslt6['qtestock']-$rslt6['qte'],
										'operation' =>$operation,
										'user' =>$_SESSION['login']
										));
			if($insere6==0)
			{
				?>
				<script language="javascript" type="text/javascript">
					alert('Echec de la mise à jour des stocks des emballages.');
					history.back();
				</script>
				<?php
			exit();	
			}
			//pour chaque consigne on fait la mise à jour des statuts 
			$stat='Consigne';
			$sql7="update consigne set statut=:statut where id_emballage='".$rslt6["id_emballage"]."' and id_sortiestock='".$id."' ";
			$req7 = $DataBase->prepare($sql7);
			$ins7 = $req7->execute(array(
										'statut' => $stat,
										
										 ));	
			if($ins7==0)
			{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la mise à jour des statuts des consignes.');
				history.back();
				</script>
			<?php
			exit();	
			}
		}

		//ici on enregistre le credit ristourne dans les charges si # de 0
		if ($_POST['ristourne']!=0)
		{
			$trouve=false;
			$sql8="select * from type_charge where id_typecharge='CREDIT_RISTOURNE'";
			$reponse8= $DataBase->query($sql8);
			while($rslt8= $reponse8->fetch())
			{
					$trouve=true;
			}
			if ($trouve==false)
			{
				//creation du type de charge
				$insere8=0;
				$statut="Actif";
				$sql="insert type_charge values  (:code,:libelle,:statut)";
				$req = $DataBase->prepare($sql);
				$insere8 = $req->execute(array(
												'code'=> 'CREDIT_RISTOURNE',
												'libelle' => 'CREDIT RISTOURNE',
												'statut'=> $statut
											)    );	
				if($insere8==0)
				{
					?>
						<script language="javascript" type="text/javascript">
						alert('Echec de l\'enregistrement du type de charge Credit Ristourne');
						history.back();
						</script>
					<?php
					exit();	
				}	
			   }
			//insertion de la charge credit ristourne
			$codecharge=0;
			$codecharge=generer_code_charge();
			$insere9=0;
			$statut="V";
			$sql="insert charge values  (:code, :id_typecharge, :montant, :date_charge, :description, :statut)";
			$req = $DataBase->prepare($sql);
			$insere9 = $req->execute(array(
											'code'=> $codecharge,
											'id_typecharge' => 'CREDIT_RISTOURNE',
											'montant' => $_POST['ristourne'],
											'date_charge' => dateFormatAnglais(date("d/m/Y")),
											'description' => strtoupper($id).'/'.$_POST['nomclient'], 
											'statut'=> $statut
										)    );	
		
			if($insere9==0)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Echec de l\'enregistrement');
					history.back();
					</script>
				<?php
				exit();	
			}
		}

		// mise à jour du statut de la vente
		$insere=0;
		$statut='V';
		$sql="update sortie_stock set login=:login, statut=:statut, fraisenlevement=:fraisenlevement, mtfacture=:mtfacture where id_sortiestock='".$id."'";
		$req = $DataBase->prepare($sql);
		$insere = $req->execute(array(
										'login' =>$_SESSION['login'],
										'statut' => $statut,
										'fraisenlevement' => $_POST['fraisenlev'],
										'mtfacture' => $_POST['mtfacture'],
										 ));	
		if($insere==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la mise à jour du statut de la vente.');
				history.back();
				</script>
			<?php
			exit();	
		}
		?>
				<script language="javascript" type="text/javascript">
				/*alert('Vente pris en compte');*/
				window.location.replace("../RecuTck.php?&Id=<?php echo $id?>");
				</script>
			<?php
			exit();
		
	}
}

?>
	