<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codecession'])&& (isset($_POST['codeart'])))
	{
		
		// on verifie si ce code sortie cession  figure deja dans la bd
		$Code=htmlentities(htmlspecialchars(strtolower($_POST["codecession"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		$sql = " select id_sortiestock from sortie_stock_cession where id_sortiestock='".$Code."' ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		$trve=true;
		 }
		if($trve==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Le code de cette cession/sortie existe deja');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{

// insertion de la sortie dans la bd
		$insere=0;
		$statut="N";
		$sql="insert into sortie_stock_cession values (:codecession,:date,:login, :observation,:statut)";
		$req = $DataBase->prepare($sql);
		$insere = $req->execute(array(
										'codecession' =>$_POST['codecession'],
										'date' =>dateFormatAnglais($_POST['date']),
										'observation' =>$_POST['observation'],
										'login' =>$_SESSION['login'],
										'statut' =>$statut
										));	
		if($insere==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de l\'enregistrement de la sortie cession.');
				history.back();
				</script>
			<?php
			exit();	
		}

// insertion de l'article 1 dans la bd	
		$insere2=0;
		$sql="insert into articlesortie_cession values (:codeart,:codecession,:qtesortie)";
		$req = $DataBase->prepare($sql);
		$insere2 = $req->execute(array(
										'codecession' =>$_POST['codecession'],
										'codeart' =>$_POST['codeart'],
										'qtesortie' =>$_POST['qtesortie'],
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
if($_POST['qtesortie2']!="")
	{
		$insere3=0;
		$sql="insert into articlesortie_cession values (:codeart,:codecession,:qtesortie)";
		$req = $DataBase->prepare($sql);
		$insere3 = $req->execute(array(
										'codecession' =>$_POST['codecession'],
										'codeart' =>$_POST['codeart2'],
										'qtesortie' =>$_POST['qtesortie2'],
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
if($_POST['qtesortie3']!="")
	{
		$insere4=0;
		$sql="insert into articlesortie_cession values (:codeart,:codecession,:qtesortie)";
		$req = $DataBase->prepare($sql);
		$insere4 = $req->execute(array(
										'codecession' =>$_POST['codecession'],
										'codeart' =>$_POST['codeart3'],
										'qtesortie' =>$_POST['qtesortie3'],
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
				window.location.replace("../index.php?formulaire=Modification_SortieCession&SC=<?php echo $_POST['codecession'];?>");
				</script>
			<?php
			exit();
	}
	}
?>