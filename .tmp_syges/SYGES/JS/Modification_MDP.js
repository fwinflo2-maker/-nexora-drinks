// JavaScript Document
 function verif_form()
 {

  if((document.getElementById('Login').value==""))
           {
            alert('BV saisir le login.');
			document.getElementById('Login').focus();
			return false;
           }
   if((document.getElementById('AncPassword').value==""))
           {
            alert('BV saisir l\'ancien mot de passe.');
			document.getElementById('AncPassword').focus();
			return false;
           }
   if((document.getElementById('NvoPassword').value==""))
           {
            alert('BV saisir le nouveau mot de passe.');
			document.getElementById('NvoPassword').focus();
			return false;
           }
   if((document.getElementById('ConPassword').value==""))
           {
            alert('BV saisir la confirmation du nouveau mot de passe.');
			document.getElementById('ConPassword').focus();
			return false;
           }
   if((document.getElementById('NvoPassword').value) != (document.getElementById('ConPassword').value ))
           {
            alert('Nouveau password et Confirmation différent.');
			document.getElementById('ConPassword').focus();
			return false;
           }
	return true;
 }// JavaScript Document