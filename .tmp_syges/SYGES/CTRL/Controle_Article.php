<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['code'])&& (isset($_POST['libelle'])))
	{
		
		// on verifie si ce Matricule  figure deja dans la bd
		$Code=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		 $sql = " select id_article from article where id_article='".$Code."' ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$trve=true;
		 }
		 
		 $trve2= false;
		 //on verifie si ce libelle figure dans la Bd
		$sql = " select libelle, id_article from article where marque='".$_POST["libelle"]."' ";
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


		// insertion dans la bd
		$insere=0;
		$Qte=0;
		$statut="Actif";
		$sql="insert into article values (:code,:libelle,:marque,:prixvente,:prixdetail,:prixrevient,:qtestock,:nbrebte,:stockfrigo,:statut,:tauxremise,:tauxristourne,:famille)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'code' =>$_POST['code'],
										'libelle' =>$_POST['libelle'],
										'marque' =>$_POST['marque'],
										'prixvente' =>$_POST['prixvente'],
										'prixdetail' =>$_POST['prixdetail'],
										'prixrevient' =>$_POST['prixrevient'],
										'qtestock' =>$Qte,
										'nbrebte' =>$_POST['nbrebte'],
										'stockfrigo' =>$Qte,
										'statut' =>$statut,
										'tauxremise' =>$_POST['tauxremise'],
										'tauxristourne' =>$_POST['tauxristourne'],
										'famille' =>$_POST['famille']
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
?>