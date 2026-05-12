<?php
	include("Connexion.php");
	include("fonctions.php");
?>
    <td><select name="codevente" id="codevente" style="width:300px;">
     <?php
	    $sql = " select id_sortiestock ,datesortiestock from sortie_stock  where statut='V' and id_sortiestock like '%".$_POST['code']."%' and id_sortiestock not in (select id_sortiestock from reglement)";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_sortiestock"]."'>";
		 echo $rslt["id_sortiestock"].' (Date :'.dateFormatFrancais($rslt["datesortiestock"]).')';
		 echo '</option>';
		 }
		 ?>
    </select> </td>

