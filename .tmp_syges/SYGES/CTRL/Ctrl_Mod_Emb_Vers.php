<?php
include("Connexion.php");
include('../fonctions.php');
//on verifie le statut de la vers
$sql5 = " select statut from versement where num_vers='".$_POST["num_vers"]."'";
$reponse5= $DataBase->query($sql5);
$rslt5= $reponse5->fetch();
if ($rslt5["statut"]=='V')
	{
			?>
				<script language="javascript" type="text/javascript">
					alert('Ce Versement est déjà validée. ');
					window.location.replace("../index.php?formulaire=Choisir_Vers_Mod");
				</script>
			<?php
			exit();	
	}
else
{

		// mise à jour dans la bd
		$insere=0;
		$sql="update emballage_vers set qte=:qte where id_emballage='".$_POST["EMB"]."' and  num_vers='".$_POST["num_vers"]."'";
		$req = $DataBase->prepare($sql);
		$insere = $req->execute(array(
										'qte' =>$_POST['qte']
										 ));	
			if($insere==0)
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
					alert('Modification effectue.');
					window.location.replace("../index.php?formulaire=Modification_Vers&Vers=<?php echo $_POST["num_vers"];?>&VD=<?php echo $_POST["vendeur"];?>");
					</script>
				<?php
				exit();
			}
}