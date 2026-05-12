<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{

	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Tarifaire.</title>
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
        function go()
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
					document.getElementById('ttarifaire').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","Ajax_Tarifaire.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id du nom
			categorie = document.getElementById('categorie').value;
			//iddept = sel.options[sel.selectedIndex].value;
			xhr.send("categorie="+categorie);
        }
        </script>
</head>

<body>
	<label for="categorie" style="margin-left:200px; width:200px;"> Selectionner Une Categorie </label></td>
    <td><select name="categorie" id="categorie" style="width:200px;" onchange="go()">
     <?php
	    $sql = " select id_categorie, libelle from categorie  where statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_categorie"]."'>";
		 echo $rslt["libelle"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>
<input type="button" value="Afficher" name="aff" onclick="go()" />
<a href="index.php?formulaire=Ajout_Tarif"> <input type="button" name="ajoutemb" id="ajoutemb" value="Ajouter Prix Vente" style="margin-left:150px; width:210px; background:#F00"/> </a>

<table id='ttarifaire' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="7"><h5>Liste des Prix de Vente par Catégorie</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td  align="left" ><h5> Libelle</h5></td>
                <td  align="</h5>"><h5>Conditionnement</h5></td>
                <td  align="left" ><h5>Catégorie </h5></td>
                <td  align="center" ><h5>Prix Vente</h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
                <td  align="center" ><h5>Supprimer</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  A.ID_ARTICLE, A.LIBELLE, A.MARQUE, A.NBREBTE, C.LIBELLE AS LIBCAT, C.ID_CATEGORIE, T.PRIXVENTE FROM ARTICLE A, CATEGORIE C, TARIFAIRE T WHERE A.ID_ARTICLE=T.ID_ARTICLE AND T.ID_CATEGORIE=C.ID_CATEGORIE ORDER BY A.ID_ARTICLE' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="left"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="</h5>"> <?php echo $rslt['MARQUE'].'('.$rslt['NBREBTE'].')'; ?> </td>
                        <td  align="left"> <?php echo $rslt['LIBCAT']; ?> </td>
                        <td  align="center"> <?php echo $rslt['PRIXVENTE'].' F'; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modification_Tarif&Cat=<?php echo $rslt['ID_CATEGORIE'];?>&Ar=<?php echo $rslt['ID_ARTICLE'];?>&Pv=<?php echo $rslt['PRIXVENTE'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                        <td  align="center"> <a href="index.php?formulaire=Suppression_Tarif&Cat=<?php echo $rslt['ID_CATEGORIE'];?>&Ar=<?php echo $rslt['ID_ARTICLE'];?>&Pv=<?php echo $rslt['PRIXVENTE'];?> "/> <img src="IMG/Supp.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>
</body>
</html>
<?php 
}
else
{
?>
				<script language="javascript" type="text/javascript">
				alert('Vous n\'etes pas habiliter a acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
