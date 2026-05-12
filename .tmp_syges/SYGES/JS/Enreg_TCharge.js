// JavaScript Document

 function verif_form()
 {	 
	
  if((document.getElementById('Code').value==""))
           {
            alert('BV saisir le Code.');
			document.getElementById('Code').focus();
			return false;
           }
 
  if((document.getElementById('Libelle').value==""))
           {
            alert('BV saisir le Libelle.');
			document.getElementById('Libelle').focus();
			return false;
           }
  if((document.getElementById('Statut').value==""))
           {
            alert('BV saisir le Statut');
			document.getElementById('Statut').focus();
			return false;
           }	
	return true;
 }
