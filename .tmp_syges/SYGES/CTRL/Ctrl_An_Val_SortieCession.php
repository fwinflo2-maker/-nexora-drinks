<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['code']))
	{
		$id=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
		//ici on verifie si la sortie cession est en instance
		$valider=false;
		$sql4=" select statut from sortie_stock_cession where id_sortiestock='".$id."'";
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
				alert('Sortie Cession encore en instance.');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
			//ici on recupere tous les emballages de la cession 
			$sql6=" select e.id_emballage, e.qtestock,e.qte as qteemb, c.id_emballage, c.qte, c.id_sortiestock from emballage e, emballagesortiecession c where c.id_sortiestock='".$_POST['code']."' and e.id_emballage=c.id_emballage";
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
			$operation='AN_VAL_CONSIGNE_SORTIE_CESSION';
			$sql="insert into mouvementemb values (id_mouv,:codeoperation,:codeemb,:date,:heure,:qte,:siqte,:sfqte,:sistock,:sfstock,:operation,:user)";
			$req = $DataBase->prepare($sql);
			$insere7 = $req->execute(array(
										'codeoperation' =>$_POST['code'],
										'codeemb' =>$rslt6["id_emballage"],
										'date' =>dateFormatAnglais($_POST['date_appro']),
										'heure' =>date('H:i'),
										'qte' =>$rslt6['qte'],
										'siqte' =>$rslt6['qteemb'],
										'sfqte' =>$rslt6['qteemb'],
										'sistock' =>$rslt6['qtestock'],
										'sfstock' => $rslt6['qtestock']+$rslt6['qte'],
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
			//ici on recupere tous les articles de la cession 
			$sql2=" select a.id_article, a.qtestock, ac.id_article, ac.qtesortie, ac.id_sortiestock from article a, articlesortie_cession ac where a.id_article=ac.id_article and ac.id_sortiestock='".$id."'";
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
			$operation='AN_VAL_SORTIE_CESSION';
			$sql="insert into mouvementar values (id_mouv,:codeoperation,:codeart,:date,:heure,:si,:qte,:sf,:operation,:user,:detenteur,:date_ann)";
			$req = $DataBase->prepare($sql);
			$insere2 = $req->execute(array(
										'codeoperation' =>$_POST['code'],
										'codeart' =>$rslt2["id_article"],
										'date' =>dateFormatAnglais($_POST['date_appro']),
										'heure' =>date('H:i'),
										'si' =>$rslt2['qtestock'],
										'qte' =>$rslt2['qtesortie'],
										'sf' =>$rslt2['qtestock']+$rslt2['qtesortie'],
										'operation' =>$operation,
										'user' =>$_SESSION['login'],
										'detenteur' =>$operation,
										'date_ann' =>date('Y/m/d')
										));	
			}
			if($insere9==0)
			{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la mise à jour des stocks des articles.');
				history.back();
				</script>
			<?php
			exit();	
			}
		
			// mise à jour dans la bd du statut de la sortie cession
			$insere=0;
			$statut='N';
			$sql="update sortie_stock_cession set login=:login, statut=:statut where id_sortiestock='".$id."'";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
											'login' =>$_SESSION['login'],
											'statut' => $statut
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
					alert('Retour en instante de la sortie cession reussie.');
					history.back();
					</script>
				<?php
				exit();
			}
	
	}
}
?>
	