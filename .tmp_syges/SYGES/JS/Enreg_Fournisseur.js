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
    if(document.getElementById('statut').value=="")
           {
            alert('BV choisir le statut.');
			document.getElementById('statut').focus();
			return false;
           }
	return true;
 }
