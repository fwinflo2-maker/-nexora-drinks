<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['code'])&& (isset($_POST['nom'])))
	{
		
		// on verifie si ce code  figure deja dans la bd
		$Code=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		$sql = " select id_client from client where id_client='".$Code."' ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		$trve=true;
		 }
		
		if($trve==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Ce Code existe deja');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{

		// insertion dans la bd
		$insere=0;
		$statut="Actif";
		$sql="insert into client values (:code,:nom,:numtel,:niu,:rc,:email,:categorie,:fraisenlevement,:tauxristourneht,:psaristournes,:statut)";
		$req = $DataBase->prepare($sql);
		$insere = $req->execute(array(
									'code' =>$_POST['code'],
									'nom' =>$_POST['nom'],
									'numtel' =>$_POST['numtel'],
									'niu' =>$_POST['niu'],
									'rc' =>$_POST['rc'],
									'email' =>$_POST['email'],
									'categorie' =>$_POST['categorie'],
									'fraisenlevement' =>$_POST['fraisenlevement'],
									'tauxristourneht' =>$_POST['tauxristourneht'],
									'psaristournes' =>$_POST['psaristournes'],
									'statut' =>$statut
									));	
		
		
		if($insere==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de l\'enregistrement');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
		?>
				<script language="javascript" type="text/javascript">
				alert('enregistrement effectue');
				window.location.replace("../index.php?formulaire=Enreg_Vente&Clt=<?php echo $_POST['code'];?>");
				</script>
			<?php
			exit();
		}
	}
	}
?>