<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codevente']))
	{
		
		//ici on verifie si la vente est encore en instance
		$id=$_POST["codevente"];
		$valider=false;
		$sql4=" select statut from sortie_stock_frigo where id_sortiestock='".$_POST['codevente']."'";
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
		else
		{
		
		// mise à jour dans la bd
		$insere=0;
		$statut='N';
		$sql="update sortie_stock_frigo set login=:login, statut=:statut where id_sortiestock='".$id."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'login' =>$_SESSION['login'],
										'statut' => $statut
										 ));	
		//ici on recupere tous les articles de la vente 
		$sql2=" select article.id_article, article.stockfrigo, articlevendu_frigo.id_article, articlevendu_frigo.qtesortie, articlevendu_frigo.id_sortiestock from article, articlevendu_frigo where articlevendu_frigo.id_sortiestock='".$id."' and article.id_article=articlevendu_frigo.id_article";
		$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			//ici pour chaque enregistrement on fait la mise a jour du stock
			$sql="update article set stockfrigo=:stockfrigo where id_article='".$rslt2["id_article"]."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'stockfrigo' => $rslt2['stockfrigo']+$rslt2['qtesortie'],
										 ));	
			//ici on enregistre dans la liste des mouvements de stocks
			$operation='AN_VAL_VENTE_FRIGO';
			$sql="insert into mouvementar_frigo values (id_mouv,:codeoperation,:codeart,:date,:heure,:si,:qte,:sf,:operation,:user)";
			$req = $DataBase->prepare($sql);
			$insere2 = $req->execute(array(
										'codeoperation' =>$id,
										'codeart' =>$rslt2["id_article"],
										'date' =>dateFormatAnglais($_POST['date_vente']),
										'heure' =>date('H:i'),
										'si' =>$rslt2['stockfrigo'],
										'qte' =>$rslt2['qtesortie'],
										'sf' =>$rslt2['stockfrigo']+$rslt2['qtesortie'],
										'operation' =>$operation,
										'user' =>$_SESSION['login']
										));
		}
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
				alert('Remise en instance de la vente reussie.');
				history.back();
				</script>
			<?php
			exit();
		}
	}
}
?>
	