<?php
	include("Connexion.php");
	include('../fonctions.php');
	
	
// on verifie si ce libelle  figure deja dans la bd
$libelle=htmlentities(htmlspecialchars(strtolower($_POST["libelle"])), ENT_QUOTES, 'UTF-8');
$trve= false;
$sql = " select id_article from article where libelle='".$libelle."' ";
$reponse= $DataBase->query($sql);
while($rslt1= $reponse->fetch())
{
	$trve=true;
	$code=$rslt1['id_article'];
 }
		
if($trve==true && ($_POST['code'] !=$code))
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

		// mise à jour dans la bd
$id=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$sql="update article set libelle=:libelle, marque=:marque, prixvente=:prixvente, prixrevient=:prixrevient,nbrebte=:nbrebte,prixdetail=:prixdetail,tauxremise=:tauxremise,tauxristourne=:tauxristourne,statut=:statut,id_famille=:famille where id_article='".$id."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'libelle' =>$_POST['libelle'],
										'marque' =>$_POST['marque'],
										'prixvente' => $_POST['prixvente'],
										'nbrebte' => $_POST['nbrebte'],
										'prixrevient' =>$_POST['prixrevient'],
										'prixdetail' =>$_POST['prixdetail'],
										'tauxremise' =>$_POST['tauxremise'],
										'tauxristourne' =>$_POST['tauxristourne'],
										'statut' =>$_POST['statut'],
										'famille' =>$_POST['famille']
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
				alert('Modification effectue');
				window.location.replace("../index.php?formulaire=Choisir_Article_Mod");
				</script>
			<?php
			exit();
		}
}
?>