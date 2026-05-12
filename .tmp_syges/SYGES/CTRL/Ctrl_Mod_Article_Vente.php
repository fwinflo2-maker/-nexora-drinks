<?php
include("Connexion.php");
include('../fonctions.php');
//on verifie le statut de la vente 
$sql5 = " select statut from sortie_stock where id_sortiestock='".$_POST["codevente"]."'";
$reponse5= $DataBase->query($sql5);
$rslt5= $reponse5->fetch();
if ($rslt5["statut"]=='V')
	{
			?>
				<script language="javascript" type="text/javascript">
					alert('Cette vente est déjà validée. ');
					window.location.replace("../index.php?formulaire=Choisir_Vente_Mod");
				</script>
			<?php
			exit();	
	}
else
{
		
		//on recupere la categorie du client
		$tr=false;
		$sql = " select id_categorie from client where id_client='".$_POST['codeclient']."' ";
		$reponse= $DataBase->query($sql);
		while($rslt6= $reponse->fetch())
		{
			$categorie=$rslt6["id_categorie"];
			$tr=true;
		}
		
		if ($tr==false)
		{
		 	?>
			<script language="javascript" type="text/javascript">
				alert('La Categorie de ce Client n\'existe pas. BV Verifier!');
				history.back();
			</script>
			<?php
			exit();	
		}
		//on recupere le prix de revient et de vente de l'article 
		$trve1=false;
		$sql = " select t.prixvente,a.prixrevient from article a, tarifaire t where a.id_article=t.id_article and t.id_categorie='".$categorie."' and t.id_article='".$_POST["codeart"]."' ";
		$reponse= $DataBase->query($sql);
		while($rslt2= $reponse->fetch())
		{
			$PV=$rslt2["prixvente"];
			$PR=$rslt2["prixrevient"];
			$trve1=true;
		}
		if($trve1==false)
		{
			?>
			<script language="javascript" type="text/javascript">
				alert('Cet Article  n\'est pas dans le tarifaire de la Categorie a laquelle appartient ce Client.');
				history.back();
			</script>
			<?php
			exit();	
		}
		// mise à jour dans la bd
		$insere=0;
		$sql="update articlevendu set qtesortie=:qtesortie, prixrevient=:prixrevient, prixvente=:prixvente, observation=:observation  where id_article='".$_POST["codeart"]."' and  id_sortiestock='".$_POST["codevente"]."'";
		$req = $DataBase->prepare($sql);
		$insere = $req->execute(array(
										'qtesortie' =>$_POST['qtevendu'],
										'prixrevient' =>$PR * $_POST['qtevendu'],
										'prixvente' =>$PV * $_POST['qtevendu'],
										'observation' =>$_POST['observationvente']
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
					window.location.replace("../index.php?formulaire=Modification_Vente&Vte=<?php echo $_POST["codevente"];?>&Clt=<?php echo $_POST["codeclient"];?>");
					</script>
				<?php
				exit();
			}
}