<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['code'])&& (isset($_POST['nom'])))
	{
		
		// on verifie si ce code  figure deja dans la bd
		$Code=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		$sql = " select id_fournisseur from fournisseur where id_fournisseur='".$Code."' ";
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
		$sql="insert into fournisseur values (:code,:nom,:numtel,:email,:statut)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'code' =>$_POST['code'],
										'nom' =>$_POST['nom'],
										'numtel' =>$_POST['numtel'],
										'email' =>$_POST['email'],
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
				/*alert('enregistrement effectue');*/
				window.location.replace("../index.php?formulaire=Enreg_Appro&Fs=<?php echo $_POST['code'];?>");
				</script>
			<?php
			exit();
		}
	}
	}
?>