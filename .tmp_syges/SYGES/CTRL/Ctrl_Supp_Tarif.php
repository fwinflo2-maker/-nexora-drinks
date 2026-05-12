<?php
	include("Connexion.php");
	include('../fonctions.php');

// suppression dans la bd
$sql="DELETE FROM TARIFAIRE WHERE ID_ARTICLE='".$_POST['codeart']."' AND ID_CATEGORIE='".$_POST['categorie']."' ";
$req = $DataBase->prepare($sql);
$insere = $req->execute();		
?>
	<script language="javascript" type="text/javascript">
		alert('Suppression effectue');
		window.location.replace("../index.php?formulaire=Tarifaire");
	</script>
<?php
exit();			
?>