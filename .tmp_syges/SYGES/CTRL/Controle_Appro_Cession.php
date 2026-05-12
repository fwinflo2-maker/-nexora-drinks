<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro'])&& (isset($_POST['codeart'])))
	{
		
		// on verifie si ce code appro  figure deja dans la bd
		$Code=htmlentities(htmlspecialchars(strtolower($_POST["codeappro"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		$sql = " select id_appro from approcession where id_appro='".$Code."' ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		$trve=true;
		 }
		if($trve==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Le code de cet appro cession existe deja');
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
		$sql="insert into approcession values (:codeappro,:date_appro,:login,:observationappro,:statut)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'codeappro' =>$_POST['codeappro'],
										'date_appro' =>dateFormatAnglais($_POST['date_appro']),
										'login' =>$_SESSION['login'],
										'observationappro' =>$_POST['observationappro'],
										'statut' =>$statut
										));	
		if($insere==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de l\'enregistrement de l\'Appro.');
				history.back();
				</script>
			<?php
			exit();	
		}
// insertion de l'article dans la bd
		$insere2=0;
		$sql="insert into article_recu_cession values (:codeappro,:codeart,:qterecu)";
		$req = $DataBase->prepare($sql);
		$insere2 = $req->execute(array(
										'codeappro' =>$_POST['codeappro'],
										'codeart' =>$_POST['codeart'],
										'qterecu' =>$_POST['qterecu'],
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
if($_POST['qterecu2']!="")
{
		$insere3=0;
		$sql="insert into article_recu_cession values (:codeappro,:codeart,:qterecu)";
		$req = $DataBase->prepare($sql);
		$insere3 = $req->execute(array(
										'codeappro' =>$_POST['codeappro'],
										'codeart' =>$_POST['codeart2'],
										'qterecu' =>$_POST['qterecu2'],
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
if($_POST['qterecu3']!="")
{
		$insere4=0;
		$sql="insert into article_recu_cession values (:codeappro,:codeart,:qterecu)";
		$req = $DataBase->prepare($sql);
		$insere4 = $req->execute(array(
										'codeappro' =>$_POST['codeappro'],
										'codeart' =>$_POST['codeart3'],
										'qterecu' =>$_POST['qterecu3'],
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
				window.location.replace("../index.php?formulaire=Modification_ApproCession&Ap=<?php echo $_POST['codeappro'];?>");
				</script>
			<?php
			exit();
		
	}
}
?>