<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro'])&& (isset($_POST['codeart'])))
	{
		
		// on verifie si ce code appro  figure deja dans la bd
		$Code=htmlentities(htmlspecialchars(strtolower($_POST["codeappro"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		$sql = " select id_appro from approfrigo where id_appro='".$Code."' ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		$trve=true;
		 }
		if($trve==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Le code de cet approvisionnement existe deja');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{

			// insertion de l'appro dans la bd
			$insere=0;
			$statut="N";
			$sql="insert into approfrigo values (:codeappro,:date_appro,:login, :observationappro,:statut)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'codeappro' =>$_POST['codeappro'],
										'date_appro' =>dateFormatAnglais($_POST['date_appro']),
										'observationappro' =>$_POST['observationappro'],
										'login' =>$_SESSION['login'],
										'statut' =>$statut
										));	
			if($insere==0)
			{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de l\'enregistrement de l\'approvisionnement.');
				history.back();
				</script>
			<?php
			exit();	
			}
			
// insertion des l'article dans la bd
	//On recupere le nbre de bouteille de l'article 1
			$sql = "Select nbrebte from article  where id_article='".$_POST['codeart']."'";
			$reponse= $DataBase->query($sql);
			$rslt3= $reponse->fetch();	
			$insere2=0;
			$sql="insert into article_recu_frigo values (:codeappro,:codeart,:qterecu,:nbrebte)";
			$req = $DataBase->prepare($sql);
			$insere2 = $req->execute(array(
										'codeappro' =>$_POST['codeappro'],
										'codeart' =>$_POST['codeart'],
										'qterecu' =>$_POST['qterecu'],
										'nbrebte' =>($rslt3['nbrebte']*$_POST['qterecu'])
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
	//On recupere le nbre de bouteille de l'article 2
if($_POST['qterecu2']!="")
{
			$sql = "Select nbrebte from article  where id_article='".$_POST['codeart2']."'";
			$reponse= $DataBase->query($sql);
			$rslt4= $reponse->fetch();	
			$insere3=0;
			$sql="insert into article_recu_frigo values (:codeappro,:codeart,:qterecu,:nbrebte)";
			$req = $DataBase->prepare($sql);
			$insere3 = $req->execute(array(
										'codeappro' =>$_POST['codeappro'],
										'codeart' =>$_POST['codeart2'],
										'qterecu' =>$_POST['qterecu2'],
										'nbrebte' =>($rslt4['nbrebte']*$_POST['qterecu2'])
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
	//On recupere le nbre de bouteille de l'article 3
if($_POST['qterecu3']!="")
{
			$sql = "Select nbrebte from article  where id_article='".$_POST['codeart3']."'";
			$reponse= $DataBase->query($sql);
			$rslt5= $reponse->fetch();	
			$insere4=0;
			$sql="insert into article_recu_frigo values (:codeappro,:codeart,:qterecu,:nbrebte)";
			$req = $DataBase->prepare($sql);
			$insere4 = $req->execute(array(
										'codeappro' =>$_POST['codeappro'],
										'codeart' =>$_POST['codeart3'],
										'qterecu' =>$_POST['qterecu3'],
										'nbrebte' =>($rslt5['nbrebte']*$_POST['qterecu3'])
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
				window.location.replace("../index.php?formulaire=Modification_Appro_Frigo&Ap=<?php echo $_POST['codeappro'];?>");
				</script>
			<?php
			exit();
	 }
}
?>