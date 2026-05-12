<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['code'])&& (isset($_POST['libelle'])))
	{
		
		// on verifie si ce code  figure deja dans la bd
		$Code=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		$sql = " select id_categorie from categorie where id_categorie='".$Code."' ";
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
		$sql="insert into categorie values (:code,:libelle,:statut,:retfiscpro,:tva)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'code' =>$_POST['code'],
										'libelle' =>$_POST['libelle'],
										'statut' =>$statut,
										'retfiscpro' =>$_POST['RetFiscPro'],
										'tva' =>$_POST['tva']
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
				alert('Enregistrement effectue');
				history.back();
				</script>
			<?php
			exit();
		}
	}
	}
?>