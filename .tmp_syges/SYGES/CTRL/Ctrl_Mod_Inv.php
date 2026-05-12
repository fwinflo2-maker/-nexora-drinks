<?php
	include("Connexion.php");
	include("../fonctions.php");
	session_start();
	if(isset($_POST['code']))
	{
		// mise à jour dans la bd
		$insere=0;
		$sql="update inventaire set soldecaisse=:soldecaisse, soldesabc=:soldesabc, soldeom=:soldeom, soldemomo=:soldemomo, creditclient=:creditclient, creditemballage=:creditemballage, soldebanque=:soldebanque, autrecredit=:autrecredit, creditbrasseries=:creditbrasseries,creditbanque=:creditbanque,ristournesclients=:ristournesclients,autresdebit=:autresdebit, palettebois=:palettebois, pu_palettebois=:pu_palettebois,paletteplastique=:paletteplastique, pu_paletteplastique=:pu_paletteplastique, emb_plein=:emb_plein, pu_emb_plein=:pu_emb_plein, emb_vide=:emb_vide, pu_emb_vide=:pu_emb_vide,val_produit=:val_produit,val_global=:val_global, user=:user where id_inv='".$_POST['code']."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
											'soldecaisse' =>$_POST['caisse'],
											'soldesabc' =>$_POST['soldesabc'],
											'soldeom' =>$_POST['soldeom'],
											'soldemomo' =>$_POST['soldemomo'],
											'creditclient' =>$_POST['creditclient'],
											'creditemballage' =>$_POST['creditemballage'],
											'soldebanque' =>$_POST['soldebanque'],
											'autrecredit' =>$_POST['autrecredit'],
											'creditbrasseries' =>$_POST['creditsabc'],
											'creditbanque' =>$_POST['creditbanque'],
											'ristournesclients' =>$_POST['ristournes'],
											'autresdebit' =>$_POST['autredebit'],
											'palettebois' =>$_POST['palettebois'],
											'pu_palettebois' =>$_POST['pupalettebois'],
											'paletteplastique' =>$_POST['paletteplastique'],
											'pu_paletteplastique' =>$_POST['pupaletteplastique'],
											'emb_plein' =>$_POST['emb_plein'],
											'pu_emb_plein' =>$_POST['pu_emb_plein'],
											'emb_vide' =>$_POST['emb_vide'],
											'pu_emb_vide' =>$_POST['pu_emb_vide'],
											'val_produit' =>0,
											'val_global' =>0,
											'user' =>$_SESSION['login']
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
				window.location.replace("../index.php?formulaire=Choisir_Inv_Mod");
				</script>
			<?php
			exit();
		}
}
?>
	