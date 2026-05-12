// JavaScript Document

 function verif_form()
 {
  if(document.getElementById('code').value=="")
           {
            alert('BV Definir le code.');
			document.getElementById('code').focus();
			return false;
           }	 
	
  if(document.getElementById('libelle').value=="")
           {
            alert('BV saisir le libelle.');
			document.getElementById('libelle').focus();
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
