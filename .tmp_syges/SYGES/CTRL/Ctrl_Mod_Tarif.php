<?php
	include("Connexion.php");
	include('../fonctions.php');
	 
	    $sql='SELECT  ID_ARTICLE FROM ARTICLE WHERE LIBELLE="'.$_POST['codeart'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt1= $reponse->fetch())
		{
			$art = $rslt1['ID_ARTICLE']; 
		}

		// mise à jour dans la bd
		$insere=0;
		$sql="update tarifaire set prixvente=:prixvente where id_article='".$_POST['codeart']."' and  id_categorie='".$_POST['categorie']."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'prixvente' =>$_POST['prixvente']
										 ));	
		
		
		if($insere==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la mise à jour.');
					history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
			?>
				<script language="javascript" type="text/javascript">
				/*alert('Modification Effectuee avec succes.');*/
				window.location.replace('../index.php?formulaire=Tarifaire')
				</script>
			<?php
			exit();	
		}

?>