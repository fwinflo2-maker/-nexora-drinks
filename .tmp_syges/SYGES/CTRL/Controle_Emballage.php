<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['code'])&& (isset($_POST['libelle'])))
	{
		
		// on verifie si ce code  figure deja dans la bd
		$Code=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		 $sql = " select id_emballage from emballage where id_emballage='".$Code."' ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		$trve=true;
		 }
		 
		 $trve2= false;
		 //on verifie si ce libelle figure dans la Bd
	$sql = " select libelle, id_emballage from emballage where libelle='".$_POST["libelle"]."' ";
	$reponse= $DataBase->query($sql);
	while($rslt2= $reponse->fetch())
		{
		$trve2=true;
		$code=$rslt2['id_article'];
 		}
		
	if($trve==true)
		{
		?>
			<script language="javascript" type="text/javascript">
			alert('Ce code existe deja. BV actualiser la fenetre');
			history.back();
			</script>
		<?php
		exit();	
		}
	else
		
		if($trve2==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Ce libelle existe deja');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{

		// insertion dans la bd
		$insere=0;
		$sql="insert into emballage values (:code,:libelle,:mt,:qte,:qtestock,:statut)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'code' =>$_POST['code'],
										'libelle' =>$_POST['libelle'],
										'mt' =>$_POST['mt'],
										'qte' =>$_POST['qte'],
										'qtestock' =>$_POST['qte'],
										'statut' =>$_POST['statut']
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
				history.back();
				</script>
			<?php
			exit();
		}
	}
}
?>