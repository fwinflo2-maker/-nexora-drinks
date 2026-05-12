<?php
	include("Connexion.php");
	include('../fonctions.php');
	
	
// on verifie si ce libelle  figure deja dans la bd
$libelle=htmlentities(htmlspecialchars(strtolower($_POST["libelle"])), ENT_QUOTES, 'UTF-8');
$trve= false;
$sql = " select id_emballage from emballage where libelle='".$libelle."' ";
$reponse= $DataBase->query($sql);
while($rslt1= $reponse->fetch())
{
	$trve=true;
	$code=$rslt1['id_emballage'];
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
$sql="update emballage set libelle=:libelle, mt_consigne=:mt, statut=:statut where id_emballage='".$id."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'libelle' =>$_POST['libelle'],
										'mt' =>$_POST['mt'],
										'statut' =>$_POST['statut']
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
				window.location.replace("../index.php?formulaire=Choisir_Emb_Mod");
				</script>
			<?php
			exit();
		}
}
?>