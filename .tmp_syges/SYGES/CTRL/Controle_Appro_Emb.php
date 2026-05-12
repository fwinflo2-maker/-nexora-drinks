<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro'])&& (isset($_POST['emb'])))
	{
		
		// on verifie si ce code appro  figure deja dans la bd
		$Code=htmlentities(htmlspecialchars(strtolower($_POST["codeappro"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		$sql = " select id_appro from approemb where id_appro='".$Code."' ";
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
		$sql="insert into approemb values (:codeappro,:date_appro,:login,:observationappro,:statut)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'codeappro' =>$_POST['codeappro'],
										'date_appro' =>dateFormatAnglais($_POST['date_appro']),
										'login' =>$_SESSION['login'],
										'observationappro' =>$_POST['observationappro'],
										'statut' =>$statut
										));	
// insertion de l'article dans la bd
		$insere2=0;
		$sql="insert into emballage_recu values (:codeappro,:emb,:qterecu)";
			$req = $DataBase->prepare($sql);
			$insere2 = $req->execute(array(
										'codeappro' =>$_POST['codeappro'],
										'emb' =>$_POST['emb'],
										'qterecu' =>$_POST['qterecu'],
										));
		
		if($insere==0 || $insere2==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de l\'enregistrement.');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
		?>
				<script language="javascript" type="text/javascript">
				/*alert('enregistrement effectue.');*/
				window.location.replace("../index.php?formulaire=Modification_Appro_Emb&Ap=<?php echo $_POST['codeappro'];?>");
				</script>
			<?php
			exit();
		}
	}
	}
?>