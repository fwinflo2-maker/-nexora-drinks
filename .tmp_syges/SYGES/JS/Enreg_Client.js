// JavaScript Document

 function verif_form()
 {
  if(document.getElementById('code').value=="")
           {
            alert('BV Definir le code.');
			document.getElementById('code').focus();
			return false;
           }	 
	
  if(document.getElementById('nom').value=="")
           {
            alert('BV saisir le nom.');
			document.getElementById('nom').focus();
			return false;
           }

 if(isNaN(document.getElementById('numtel').value))
           {
            alert('Le numero de telephone est un numérique!');
			document.getElementById('numtel').focus();
			return false;
           }
    if(document.getElementById('categorie').value=="")
           {
            alert('BV choisir la categorie.');
			document.getElementById('categorie').focus();
			return false;
           }
   if(isNaN(document.getElementById('fraisenlevement').value)|| (document.getElementById('fraisenlevement').value==""))
           {
            alert('Les frais d\'enlevement est un numérique!');
			document.getElementById('fraisenlevement').focus();
			return false;
           }
   if(isNaN(document.getElementById('fraisenlevement_pet').value)|| (document.getElementById('fraisenlevement_pet').value==""))
           {
            alert('Les frais d\'enlevement PET est un numérique!');
			document.getElementById('fraisenlevement_pet').focus();
			return false;
           }
    if(document.getElementById('statut').value=="")
           {
            alert('BV choisir le statut.');
			document.getElementById('statut').focus();
			return false;
           }
	return true;
 }
