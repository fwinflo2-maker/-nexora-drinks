<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codevente'])&& (isset($_POST['codeart'])))
	{
		//on recupere le prix de revient et au detail de vente de l'article 1
		$sql = " SELECT * FROM ARTICLE WHERE ID_ARTICLE ='".$_POST["codeart"]."' ";
		$reponse= $DataBase->query($sql);
		while($rslt2= $reponse->fetch())
		{
			$prixdetail1=$rslt2["PRIXDETAIL"];
			$prixrevient1=$rslt2["PRIXREVIENT"]/$rslt2["NBREBTE"];
		 }
		//on recupere le prix de revient et au detail de vente de l'article 2
		if($_POST['qtevendu2']!="")
		{
			$sql = " SELECT * FROM ARTICLE WHERE ID_ARTICLE ='".$_POST["codeart2"]."' ";
			$reponse= $DataBase->query($sql);
			while($rslt2= $reponse->fetch())
			{
				$prixdetail2=$rslt2["PRIXDETAIL"];
				$prixrevient2=$rslt2["PRIXREVIENT"]/$rslt2["NBREBTE"];
		 	}
		}
		//on recupere le prix de revient et au detail de vente de l'article 3
		if($_POST['qtevendu3']!="")
		{
			$sql = " SELECT * FROM ARTICLE WHERE ID_ARTICLE ='".$_POST["codeart3"]."' ";
			$reponse= $DataBase->query($sql);
			while($rslt2= $reponse->fetch())
			{
				$prixdetail3=$rslt2["PRIXDETAIL"];
				$prixrevient3=$rslt2["PRIXREVIENT"]/$rslt2["NBREBTE"];
		 	}
		}
		// on verifie si ce code vente  figure deja dans la bd
		$Code=htmlentities(htmlspecialchars(strtolower($_POST["codevente"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		$sql = " select id_sortiestock from sortie_stock_frigo where id_sortiestock='".$Code."' ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		$trve=true;
		 }
		if($trve==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Le code de cette perte existe deja');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{

// insertion de la vente dans la bd
$insere=0;
$statut="N";
$sql="insert into sortie_stock_frigo values (:codevente,:date_vente,:observationsortie,:login,:statut)";
$req = $DataBase->prepare($sql);
$insere = $req->execute(array(
										'codevente' =>$_POST['codevente'],
										'date_vente' =>dateFormatAnglais($_POST['date_vente']),
										'observationsortie' =>$_POST['observationsortie'],
										'login' =>$_SESSION['login'],
										'statut' =>$statut
										));	
if($insere==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de l\'enregistrement de la perte.');
				history.back();
				</script>
			<?php
			exit();	
		}
//on recupere l'ID du paiement de frais pour l'affichage du recu
		$lastid=$DataBase->lastInsertId();
// insertion de l'article 1 dans la bd
$insere2=0;
$prixvente=0;
$observation="PERTE";
$sql="insert into articlevendu_frigo values (:codeart,:codevente,:qtevendu,:prixrevient,:prixvente,:observationvente)";
$req = $DataBase->prepare($sql);
$insere2 = $req->execute(array(
										'codeart' =>$_POST['codeart'],
										'codevente' =>$_POST['codevente'],
										'qtevendu' =>$_POST['qtevendu'],
										'prixrevient' =>$_POST['qtevendu']*$prixrevient1,
										'prixvente' =>$prixvente,
										'observationvente' =>$observation,
										));
if($insere2==0)
	{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de l\'enregistrement de l\'article 1.');
				history.back();
				</script>
			<?php
			exit();	
	}
	
// insertion de l'article 2 dans la bd
if($_POST['qtevendu2']!="")
{
	$insere3=0;
	$prixvente2=0;
	$observation="PERTE";
	$sql="insert into articlevendu_frigo values (:codeart,:codevente,:qtevendu,:prixrevient,:prixvente,:observationvente)";
	$req = $DataBase->prepare($sql);
	$insere3 = $req->execute(array(
										'codeart' =>$_POST['codeart2'],
										'codevente' =>$_POST['codevente'],
										'qtevendu' =>$_POST['qtevendu2'],
										'prixrevient' =>$_POST['qtevendu2']*$prixrevient2,
										'prixvente' =>$prixvente2,
										'observationvente' =>$observation,
										));
	if($insere3==0)
	{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de l\'enregistrement de l\'article 2.');
				history.back();
				</script>
			<?php
			exit();	
	}
}
// insertion de l'article 3 dans la bd
if($_POST['qtevendu3']!="")
{
	$insere4=0;
	$prixvente3=0;
	$observation="PERTE";
	$sql="insert into articlevendu_frigo values (:codeart,:codevente,:qtevendu,:prixrevient,:prixvente,:observationvente)";
	$req = $DataBase->prepare($sql);
	$insere4 = $req->execute(array(
										'codeart' =>$_POST['codeart3'],
										'codevente' =>$_POST['codevente'],
										'qtevendu' =>$_POST['qtevendu3'],
										'prixrevient' =>$_POST['qtevendu3']*$prixrevient3,
										'prixvente' =>$prixvente3,
										'observationvente' =>$observation,
										));
	if($insere4==0)
	{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de l\'enregistrement de l\'article 3.');
				history.back();
				</script>
			<?php
			exit();	
	}	
}

		?>
				<script language="javascript" type="text/javascript">
				/*alert('enregistrement effectue.');*/
				window.location.replace("../index.php?formulaire=Modification_Perte&Vte=<?php echo $_POST['codevente'];?>");
				</script>
			<?php
			exit();
	}
}
?>