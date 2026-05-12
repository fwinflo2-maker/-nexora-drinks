<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"|| $_SESSION['habilitation']=="OPS"))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql2 = " select *  from sortie_stock  where id_sortiestock='".$_GET['Vte']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
?>
<!DOCTYPE html >
<html >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de modification d'une Vente.</title>
<style type="text/css">
label
{
	display:block;
	width:110px;
	float: left;
	}
</style>
<script src="JS/Enreg_Vente.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
<script type='text/javascript'>
//FONCTION POUR TESTER SI LE NAVIGATEUR PEUT GERER AJAX
	function getXhr()
			{
				var xhr = null;
				if(window.XMLHttpRequest) // Firefox et autres
					xhr = new XMLHttpRequest();
				else if(window.ActiveXObject)
				{ // Internet Explorer
					try {
							xhr = new ActiveXObject("Msxml2.XMLHTTP");
						} 
					catch (e) {
						xhr = new ActiveXObject("Microsoft.XMLHTTP");
						}
				}
				else 
				{ // XMLHttpRequest non supporté par le navigateur
					alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
					xhr = false;
				}
				return xhr;
			}
        /**
        * Méthode qui sera appelée sur le click du bouton
        */
		function go2()
		{
			var xhr = getXhr();
			// On défini ce qu'on va faire quand on aura la réponse
			xhr.onreadystatechange = function()
			{
				// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
				if(xhr.readyState == 4 && xhr.status == 200)
				{
					leselect = xhr.responseText;
					// On se sert de innerHTML pour rajouter les options a la liste
					document.getElementById('codeclient').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","Ajax_Select_Clt.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id du nom
			nomclt = document.getElementById('nomclt').value;
			//iddept = sel.options[sel.selectedIndex].value;
			xhr.send("nomclt="+nomclt);
        }
</script>
</head>
 
<body>

<form action="CTRL/Ctrl_Mod_Vente.php" method="post" onSubmit="return verif_form()" >
<fieldset style=" width:1050px;"><legend>Informations sur la vente à modifier</legend>
<table >
<tr>
	<td><label for="codevente"> Code * </label></td>
    <td><input type="text" id="codevente" name="codevente" readonly="readonly" style="width:150px; background-color:#ECECEC" value="<?php echo $rslt2['ID_SORTIESTOCK']; ?>"/></td>
<?php if ($_SESSION['habilitation']=="Administrateur")
{
?>
    <td><label for="date_vente"> Date vente * </label> </td>
    <td><input type="text" id="date_vente" name="date_vente" style="width:100px;" value="<?php echo dateFormatFrancais($rslt2['DATESORTIESTOCK']); ?>"/> <input type="button" value="Calendrier" onClick="displayCalendar(document.forms[0].date_appro,'dd/mm/yyyy',this)" /></td>
 <?php 
}
else
{
?>
    <td><label for="date_vente"> Date vente * </label> </td>
    <td><input type="text" id="date_vente" name="date_vente" style="width:200px ; background-color:#ECECEC" value="<?php echo dateFormatFrancais($rslt2['DATESORTIESTOCK']); ?>" readonly="readonly"/>
<?php 
}
?>
	<td><label for="observationsortie"> Observation  </label></td>
    <td><input type="text" id="observationsortie" name="observationsortie" maxlength="35" style="width:280px;" value="<?php echo $rslt2['OBSERVATION']; ?>"/></td>
</tr>
<tr>
<?php if ($_SESSION['habilitation']=="Administrateur")
{
?>
    <td><label for="ristourne"> Crédit Ristourne * </label></td>
    <td><input type="text" id="ristourne" name="ristourne" style="width:150px" value="<?php echo $rslt2['CREDITRISTOURNE']; ?>"></td>
 <?php 
}
else
{
?>
    <td><label for="ristourne"> Crédit Ristourne * </label></td>
    <td><input type="text" id="ristourne" name="ristourne" style="width:150px; background-color:#ECECEC" readonly="readonly" value="<?php echo $rslt2['CREDITRISTOURNE']; ?>"></td>
<?php 
}
?>
	<td><label for="nomclt"> Mot Clé (Client) </label></td>
    <td><input type="text" id="nomclt" name="nomclt" style="width:200px;" onChange="go2()"/></td>
    <td><label for="codeclient"> Client * </label></td>
    <td><select name="codeclient" id="codeclient" style="width:280px;">
    
         <?php
//		$fraisenlevement=0;
//		$ttfraisenlevement=0;
//		$sql5 = " select fraisenlevement from client  where id_client='".$rslt2["ID_CLIENT"]."' and statut='Actif' order by nom  ";
//		$reponse5= $DataBase->query($sql5);
//		while($rslt5= $reponse5->fetch())
//		{	
//		 $fraisenlevement=$rslt5["fraisenlevement"];
//		 }
	   $fraisenlevement_cassier=0;
		$fraisenlevement_pet=0;
		$ttfraisenlevement=0;
		$sql5 = " select fraisenlevement, fraisenlevement_pet from client  where id_client='".$rslt2["ID_CLIENT"]."'";
		$reponse5= $DataBase->query($sql5);
		while($rslt5= $reponse5->fetch())
		{	
		 	$fraisenlevement_cassier=$rslt5["fraisenlevement"];
			$fraisenlevement_pet=$rslt5["fraisenlevement_pet"];
		 }
		 
		$sql4 = " select id_client ,nom from client  where statut='Actif' order by nom  ";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
			
		if ($rslt4["id_client"]==$rslt2["ID_CLIENT"])
		{	
			 echo "<option selected value='".$rslt4["id_client"]."'>";
		}
		else
		{
			 echo "<option value='".$rslt4["id_client"]."'>";
		}
			 echo $rslt4["nom"];
			 echo '</option>';
		
		 }
		 ?>
    </select> </td>
</tr>
<tr>
    <td colspan="6" align="right"><input type="submit" align="left" value="Modifier" id="Modifier" name="Modifier"/></td>
</tr>

</table>
</fieldset>
<table>
<tr>
		<td align="center" > <a href="index.php?formulaire=Validation_Vente&Vte=<?php echo $rslt2['ID_SORTIESTOCK'];?>"> <input type="button" name="Valider" id="Valider" value="Fin de Saisie" style="margin-left:10px; background:#F00;"/> </a></td>
    	<td align="center" > <a href="index.php?formulaire=Ajout_Consigne_Vte&Vte=<?php echo $rslt2['ID_SORTIESTOCK'];?>&DateV=<?php echo $rslt2['DATESORTIESTOCK'];?> "> <input type="button" name="ajoutemb" id="ajoutemb" value="Consigner Emballage" style="margin-left:100px; width:210px"/> </a></td>
       <td align="center" > <a href="index.php?formulaire=Deconsignation_Emb&Vte=<?php echo $rslt2['ID_SORTIESTOCK'];?>&DateV=<?php echo $rslt2['DATESORTIESTOCK'];?> "> <input type="button" name="Deconsignation" id="Deconsignation" value="Deconsigner Emballage" style="margin-left:100px; width:210px"/> </a></td>
	<td align="center" > <a href="index.php?formulaire=Ajout_Article_Vente&Vte=<?php echo $rslt2['ID_SORTIESTOCK'];?>&Clt=<?php echo $rslt2["ID_CLIENT"];?> "> <input type="button" name="ajoutart" id="ajoutart" value="Ajouter des articles" style="margin-left:100px; width:210px"/> </a></td>
</tr>
</table>
<table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h5>Liste des Articles de la vente</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	<td align="center"><h5>Code </h5></td>
                <td  align="center"><h5>Conditionnement </h5></td>
                <td  align="center" ><h5>Libelle </h5></td>
                <td  align="center" ><h5>Qte </h5></td>
                <td  align="center" ><h5>Prix de Vente </h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Modifier</h5> </td>
                <td  align="center" ><h5>Supprimer</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
$i = 0;
$MT=0;			
$ttcolis=0;
$nbrecasier=0;
$nbrepet=0;
$sql='SELECT  A.ID_ARTICLE, A.LIBELLE, A.MARQUE, A.NBREBTE, A.QTESTOCK, AV.ID_ARTICLE, AV.QTESORTIE, AV.ID_SORTIESTOCK, AV.PRIXREVIENT, AV.PRIXVENTE, AV.OBSERVATION FROM ARTICLE A, ARTICLEVENDU AV WHERE A.ID_ARTICLE=AV.ID_ARTICLE AND AV.ID_SORTIESTOCK="'.$_GET['Vte'].'" ORDER BY AV.ID_ARTICLE ' ;
	$reponse= $DataBase->query($sql);
		while($rslt3= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt3['ID_ARTICLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['MARQUE'].' '.$rslt3['NBREBTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['LIBELLE'].' (Stock :'.$rslt3['QTESTOCK'].')'; ?> </td>
                        <td  align="center"> <?php echo $rslt3['QTESORTIE']; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt3['PRIXVENTE'], 0, ',', ' ').' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $rslt3['OBSERVATION']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modifier_Article_Vente&AR=<?php echo $rslt3['ID_ARTICLE'];?>&Vte=<?php echo $rslt3['ID_SORTIESTOCK']; ?>&Qte=<?php echo $rslt3['QTESORTIE'];?>&Clt=<?php echo $rslt2["ID_CLIENT"];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                        <td  align="center"> <a href="index.php?formulaire=Supprimer_Article_Vente&AR=<?php echo $rslt3['ID_ARTICLE'];?>&Vte=<?php echo $rslt3['ID_SORTIESTOCK']; ?>"/> <img src="IMG/Supp.png"/> </a></td>
                     </tr>
                <?php
				$i++;
				$MT=$MT+$rslt3['PRIXVENTE'];
				$ttcolis=$ttcolis+$rslt3['QTESORTIE'];
				//on compte les casiers
				if(($rslt3['MARQUE']=="CASIER") || ($rslt3['MARQUE']=="casier")|| ($rslt3['MARQUE']=="CASIERS")|| ($rslt3['MARQUE']=="casiers"))
				{
					$nbrecasier=$nbrecasier+$rslt3['QTESORTIE'];
				}
				 
		 }
		 $nbrepet=$ttcolis-$nbrecasier;
		 $ttfraisenlevement=($fraisenlevement_cassier*$nbrecasier)+($fraisenlevement_pet*$nbrepet);				?>
 <tr bgcolor="#E0E0E0"> 
        <td align="center" colspan="2"><h4> Total Colis :  <?php echo number_format($ttcolis, 0, ',', ' '); ?></h4></td>
    <td align="center" colspan="2"><h4> Total Casiers :  <?php echo number_format($nbrecasier, 0, ',', ' '); ?></h4></td>
    <td align="center" colspan="4"><h4> Total Articles :  <?php echo number_format($MT, 0, ',', ' ').' Franc CFA'; ?></h4></td>
</tr>
 <tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="4"><h4> PU Enlevement(FCFA) :Cassier=  <?php echo number_format($fraisenlevement_cassier, 0, ',', ' ').'/PET='.number_format($fraisenlevement_pet, 0, ',', ' '); ?></h4></td> 
    <td align="center" colspan="4"><h4> Montant Frais Enlevement :  <?php echo number_format($ttfraisenlevement, 0, ',', ' ').' Franc CFA'; ?></h4></td>
</tr>
</table>

<table id='tconsigne' border="0" width="100%" align="center">
          <tr align="center" >
          	<td colspan="6"><h5>Consigne Emballage</h5></td>
          </tr>
          <tr bgcolor="#CCCCCC">
            	<td align="center"><h5>Code </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td  align="center" ><h5>Quantite </h5></td>
                <td  align="center" ><h5>PU Cons.</h5></td>
                <td  align="center" ><h5>Montant Cons.</h5></td>
                <td  align="center" ><h5>Supprimer</h5></td>
		</tr>
			
<?php
$color = "darkgray";
$i = 0;
$MTC=0;			
$sql='SELECT  E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE,E.QTESTOCK, C.MONTANT, E.ID_EMBALLAGE ,C.ID_SORTIESTOCK ,C.QTE, C.STATUT FROM EMBALLAGE E, CONSIGNE C WHERE C.ID_EMBALLAGE =E.ID_EMBALLAGE AND C.ID_SORTIESTOCK="'.$_GET['Vte'].'" ORDER BY C.ID_EMBALLAGE ' ;
	$reponse= $DataBase->query($sql);
		while($rslt3= $reponse->fetch())
		{
			if ($i%2 == 0)
					$color = '#E0E0E0';
				else
					$color = "white";
				echo "<tr bgcolor=$color>";
				?>
                        <td  align="center"> <?php echo $rslt3['ID_EMBALLAGE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['LIBELLE'].' (Stock :'.$rslt3['QTESTOCK'].')'; ?> </td>
                        <td  align="center"> <?php echo $rslt3['QTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['MT_CONSIGNE'].' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $rslt3['MONTANT'].' FCFA'; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Supp_Consigne_Vte&Vte=<?php echo $rslt2['ID_SORTIESTOCK']; ?>&Emb=<?php echo $rslt3['ID_EMBALLAGE']; ?> "/> <img src="IMG/Supp.png"/> </a></td>
                     </tr>
                <?php
				$i++;
				$MTC=$MTC+$rslt3['MONTANT'];
		 }
				?>
 <tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="6"><h4> Total Consigne :  <?php echo number_format($MTC, 0, ',', ' ').' Franc CFA'; ?></h4></td>
</tr>
</table>
<table id='trtr' border="0" width="100%" align="center">
          <tr align="center" >
          	<td colspan="6"><h5>Déconsignation Emballage</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	<td align="center"><h5>Code </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td  align="center" ><h5>Quantite </h5></td>
                <td  align="center" ><h5>PU.</h5></td>
                <td  align="center" ><h5>Montant</h5></td>
                <td  align="center" ><h5>Supprimer</h5></td>
			</tr>
<?php
$color = "darkgray";
$i = 0;
$MTR=0;			

$sql='SELECT  E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, RE.ID_RTREMB, RE.MONTANT, RE.ID_EMBALLAGE ,RE.ID_SORTIESTOCK ,RE.QTE, RE.STATUT FROM EMBALLAGE E, RTREMBVTE RE WHERE RE.ID_EMBALLAGE =E.ID_EMBALLAGE AND RE.ID_SORTIESTOCK="'.$_GET['Vte'].'" ORDER BY RE.ID_EMBALLAGE ' ;
$reponse= $DataBase->query($sql);
		while($rslt3= $reponse->fetch())
		{
			if ($i%2 == 0)
					$color = '#E0E0E0';
				else
					$color = "white";
				echo "<tr bgcolor=$color>";
				?>
                        <td  align="center"> <?php echo $rslt3['ID_EMBALLAGE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['QTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['MT_CONSIGNE'].' F'; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt3['MONTANT'], 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Supp_Emb_Rtr_Vte&Id=<?php echo $rslt3['ID_RTREMB']; ?>"/> <img src="IMG/Supp.png"/> </a></td>
                     </tr>
                <?php
				$i++;
				$MTR=$MTR+$rslt3['MONTANT'];
		 }
				?>
 <tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="6"><h4> Total Retour Emballage :  <?php echo number_format($MTR, 0, ',', ' ').' Franc CFA'; ?></h4></td>
</tr>
<tr>
    <td align="center" colspan="6"><h3>Montant Total Vente :  <?php echo number_format($MTC+$MT+$ttfraisenlevement-$MTR-$rslt2['CREDITRISTOURNE'], 0, ',', ' ').' Franc CFA'; ?></h3></td>
</tr>
</table>
</form>
</body>
</html>
<?php 
}
else
{
?>
				<script language="javascript" type="text/javascript">
				alert('Vous n\'etes pas habiliter a  acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
