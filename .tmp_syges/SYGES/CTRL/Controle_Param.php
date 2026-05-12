<?php
	include("Connexion.php");
	include('../fonctions.php');
	
	$trve=false;
	$sql='SELECT  * FROM PARAMETRE ' ;
 	$reponse= $DataBase->query($sql);
	while($rslt= $reponse->fetch())
		{
			$trve=true;
		}
	if($trve==true)
		{
			// mise à jour dans la bd
			$insere=0;
			$sql="update parametre set psa=:psa, exercice=:exercice, tva=:tva, tauxcacorrespondant=:tauxcacorrespondant, tauxacompteib=:tauxacompteib,tauxepargne=:tauxepargne,tauxremisesht=:tauxremisesht,tauxristournesht=:tauxristournesht,psaristournes=:psaristournes, psaremise=:psaremise,tauxretfiscpro=:tauxretfiscpro,bonuscasse=:bonuscasse,depotgarantie=:depotgarantie,precompte=:precompte, obannu=:annuel, objanv=:janv, obfevr=:fevr, obmars=:mars, obavri=:avri, obmai=:mai, objuin=:juin, objuil=:juil, obaout=:aout, obsept=:sept, obocto=:octo, obnove=:nove, obdece=:dece where id_parametre='".$_POST['code']."'";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
											'psa' =>$_POST['psa'],
											'exercice' => $_POST['exercice'],
											'tva' =>$_POST['tva'],
											'tauxcacorrespondant' =>$_POST['tauxcacorrespondant'],
											'tauxacompteib' =>$_POST['tauxacompteib'],
											'tauxepargne' =>$_POST['tauxepargne'],
											'tauxremisesht' =>$_POST['tauxremisesht'],
											'tauxristournesht' =>$_POST['tauxristournesht'],
											'psaristournes' =>$_POST['psaristournes'],
											'psaremise' =>$_POST['psaremise'],
											'tauxretfiscpro' =>$_POST['RetFiscPro'],
											'bonuscasse' =>$_POST['bonuscasse'],
											'depotgarantie' =>$_POST['depotgarantie'],
											'precompte' =>$_POST['precompte'],
											'annuel' =>$_POST['annuel'],
											'janv' =>$_POST['janv'],
											'fevr' =>$_POST['fevr'],
											'mars' =>$_POST['mars'],
											'avri' =>$_POST['avri'],
											'mai' =>$_POST['mai'],
											'juin' =>$_POST['juin'],
											'juil' =>$_POST['juil'],
											'aout' =>$_POST['aout'],
											'sept' =>$_POST['sept'],
											'octo' =>$_POST['octo'],
											'nove' =>$_POST['nove'],
											'dece' =>$_POST['dece']
											));	
			
			
			if($insere==0)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Echec de la mise a jour.');
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
					window.location.replace("../index.php?formulaire=Accueil");
					</script>
				<?php
				exit();
			}
		}
	else
		{
			//Enregistrement dans la BD
			if(isset($_POST['psa'])&& (isset($_POST['tva']))&& (isset($_POST['tauxacompteib']))&& (isset($_POST['exercice']))&& (isset($_POST['tauxcacorrespondant'])))
			{
		
				// insertion dans la bd
				$insere2=0;
				$sql="insert into parametre values (id_parametre,:psa,:exercice,:tva,:tauxcacorrespondant,:tauxacompteib,:tauxepargne,:tauxremisesht,:psaremise,:tauxretfiscpro,:bonuscasse,:depotgarantie,:precompte, :annuel, :janv, :fevr, :mars, :avri, :mai, :juin, :juil, :aout, :sept, :octo, :nove, :dece)";
					$req = $DataBase->prepare($sql);
					$insere2 = $req->execute(array(
												'psa' =>$_POST['psa'],
												'exercice' =>$_POST['exercice'],
												'tva' =>$_POST['tva'],
												'tauxcacorrespondant' =>$_POST['tauxcacorrespondant'],
												'tauxacompteib' =>$_POST['tauxacompteib'],
												'tauxepargne' =>$_POST['tauxepargne'],
												'tauxremisesht' =>$_POST['tauxremisesht'],
												'psaremise' =>$_POST['psaremise'],
												'tauxretfiscpro' =>$_POST['RetFiscPro'],
												'bonuscasse' =>$_POST['bonuscasse'],
												'depotgarantie' =>$_POST['depotgarantie'],
												'precompte' =>$_POST['precompte'],
												'annuel' =>$_POST['annuel'],
												'janv' =>$_POST['janv'],
												'fevr' =>$_POST['fevr'],
												'mars' =>$_POST['mars'],
												'avri' =>$_POST['avri'],
												'mai' =>$_POST['mai'],
												'juin' =>$_POST['juin'],
												'juil' =>$_POST['juil'],
												'aout' =>$_POST['aout'],
												'sept' =>$_POST['sept'],
												'octo' =>$_POST['octo'],
												'nove' =>$_POST['nove'],
												'dece' =>$_POST['dece']
												));	
				
				if($insere2==0)
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
						window.location.replace("../index.php?formulaire=Accueil");
						</script>
					<?php
					exit();
				}
		  }
	   }
		
?>