<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro'])&& (isset($_POST['emb'])))
	{
		//ici on recupere le code de l'emballage et la qte en stock
		$sql='SELECT ID_EMBALLAGE, QTESTOCK, QTE FROM EMBALLAGE WHERE LIBELLE="'.$_POST['emb'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt1= $reponse->fetch())
		{
			$emb = $rslt1['ID_EMBALLAGE'];  
			$st=$rslt1['QTESTOCK'];
			$qt=$rslt1['QTE'];
		}
		//ici on verifie si la consigne est deja deconsignee
		$trve=false;
		$sql2='select statut from consigneapp where id_consigne="'.$_POST['codeconsigne'].'"' ;
		$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
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
			//On verifie si la quantité sollicitee est en stock
			$trve3=false;
			if (($st < $_POST['qte'])||($qt < $_POST['qte']))
				{
					$trve3=true;
				}
			if ($trve3==true)
			{
				?>
				<script language="javascript" type="text/javascript">
						alert('Quantité des emballages disponible en stock inférieur à la quantité à déconsigner.');
						history.back();
				</script>
				<?php
				exit();	
			}
			else
			{
				// mise à jour de la consigne et enregistrement deconsigne
				$insere=0;
				$insere2=0;
				$statut='Deconsigne';
				$sql4="update consigneapp set statut=:statut, obs_deconsigne=:obs, date_deconsigne=:date where id_consigne='".$_POST['codeconsigne']."'";
				$req4 = $DataBase->prepare($sql4);
				$insere4 = $req4->execute(array(
												'statut' =>$statut,
												'obs'=> $_POST['obs'],
												'date'=> dateFormatAnglais($_POST['Dat']),
												 ));	
				// mise à jr du stock de l'emballage
				$sql2="update emballage set qte=:qte, qtestock=:qtestock where id_emballage='".$emb."'";
				$req2 = $DataBase->prepare($sql2);
				$insere2 = $req2->execute(array(
											'qte' =>$qt-$_POST['qte'],
											'qtestock' =>$st-$_POST['qte'],
											 ));
			
			
				if($insere4==0 || $insere2==0)
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
					$sql='SELECT  ID_FOURNISSEUR FROM APPROVISIONNEMENT WHERE ID_APPRO="'.$_POST['codeappro'].'" ' ;
					$reponse= $DataBase->query($sql);
					while($rslt8= $reponse->fetch())
					{
						$Fs = $rslt8['ID_FOURNISSEUR']; 
					}
					?>
			
					<script language="javascript" type="text/javascript">
					alert('Deconsignation enregistrée.');
					window.location.replace("../index.php?formulaire=Enreg_Consigne_Appro&Ap=<?php echo $_POST["codeappro"];?>&Fs=<?php echo $Fs;?>");
					</script>
					<?php
					exit();
				}
			}
		}
	}
?>