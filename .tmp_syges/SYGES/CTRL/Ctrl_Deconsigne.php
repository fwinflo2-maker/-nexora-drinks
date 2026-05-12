<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codevente'])&& (isset($_POST['emb'])))
	{
		//ici on recupere le code de l'emballage et la qte en stock
		$sql='SELECT ID_EMBALLAGE, QTESTOCK FROM EMBALLAGE WHERE LIBELLE="'.$_POST['emb'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt1= $reponse->fetch())
		{
			$emb = $rslt1['ID_EMBALLAGE']; 
			$st = $rslt1['QTESTOCK']; 
		}
		//ici on verifie si la consigne est deja deconsignee
		$trve=false;
		$sql='select statut from consigne where id_emballage="'.$emb.'" and  id_sortiestock="'.$_POST['codevente'].'"' ;
		$reponse= $DataBase->query($sql);
		while($rslt2= $reponse->fetch())
		{
			if ($rslt2['statut']=='Deconsigne')
			{
				$trve=true; 
			}
		}
		if($trve==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Cet emballage est deja deconsignee');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
			// mise à jour dans la BD
			$insere=0;
			$insere2=0;
			$statut='Deconsigne';
			$sql="update consigne set statut=:statut, obs_deconsigne=:obs, date_deconsigne=:date where id_emballage='".$emb."' and  id_sortiestock='".$_POST['codevente']."'";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
											'statut' =>$statut,
											'obs'=> $_POST['obs'],
											'date'=> dateFormatAnglais($_POST['Dat']),
											 ));	
				$sql2="update emballage set qtestock=:qtestock where id_emballage='".$emb."'";
				$req2 = $DataBase->prepare($sql2);
				$insere2 = $req2->execute(array(
											'qtestock' =>$st+$_POST['qte'],
											 ));
			
			
			if($insere==0 || $insere2==0)
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
			$sql='SELECT  ID_CLIENT FROM SORTIE_STOCK WHERE ID_SORTIESTOCK="'.$_POST['codevente'].'" ' ;
			$reponse= $DataBase->query($sql);
			while($rslt3= $reponse->fetch())
			{
				$Client = $rslt3['ID_CLIENT']; 
			}
			?>
			
					<script language="javascript" type="text/javascript">
					alert('Modification effectue');
					window.location.replace("../index.php?formulaire=Enreg_Consigne&Vte=<?php echo $_POST["codevente"];?>&Clt=<?php echo $Client;?>");
					</script>
				<?php
				exit();
			}
	}
}
?>