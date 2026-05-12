<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['categorie'])&& (isset($_POST['prixvente'])))
	{
		// on verifie si cet article n'est pas deja dans cettecategorie  
			$trve=false;
			$sql = " select * from tarifaire where id_categorie='".$_POST["categorie"]."' and id_article='".$_POST["codeart"]."' ";
			$reponse= $DataBase->query($sql);
			while($rslt= $reponse->fetch())
			{
				$trve=true;
		 	}
			if($trve==true)
			{
			?>
				<script language="javascript" type="text/javascript">
				alert('Cet Article est deja renregistre pour cette categorie.');
				history.back();
				</script>
			<?php
			exit();	
			}

	// insertion du tafif dans la bd
			$insere2=0;
			$sql="insert into tarifaire values (:codecat,:codeart,:prixvente)";
			$req = $DataBase->prepare($sql);
			$insere2 = $req->execute(array(
											'codecat' =>$_POST['categorie'],
											'codeart' =>$_POST['codeart'],
											'prixvente' =>$_POST['prixvente']
											));
	
			
			if($insere2==0)
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
					history.back();
					</script>
				<?php
				exit();
			}
	}
?>