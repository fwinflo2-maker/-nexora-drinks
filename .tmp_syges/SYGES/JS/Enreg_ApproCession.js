// JavaScript Document
function verif_date(input)
{
var regex = new RegExp("[/-]");
var date = input.split(regex);
var nbJours = new Array('',31,28,31,30,31,30,31,31,30,31,30,31);
var result = true;

if ( date['2']%4 == 0 && date['2']%100 > 0 || date['2']%400 == 0 )
nbJours['2'] = 29;

if( isNaN(date['2']) )
result=false;

if ( isNaN(date['1']) || date['1'] > 12 || date['1'] < 1 )
result=false;

if ( isNaN(date['0']) || date['0'] > nbJours[Math.round(date['1'])] || date['0'] < 1 )
result=false;

return result;
}
 function verif_form()
 {
  if(document.getElementById('codeappro').value=="")
           {
            alert('BV saisir le code de l\'appro.');
			document.getElementById('codeappro').focus();
			return false;
           }
   if(!verif_date(document.getElementById('date_appro').value))
           {
            alert('Date de l\'appro  incorrecte.');
			document.getElementById('date_appro').focus();
			return false;
           }

  if(document.getElementById('codeart').value=="")
           {
            alert('BV choisir le code de l\'article.');
			document.getElementById('codeart').focus();
			return false;
           }	
		   
 if(isNaN(document.getElementById('qterecu').value)||(document.getElementById('qterecu').value=="")||(document.getElementById('qterecu').value==0))
           {
            alert('Le quantite recu est un numérique et diff de zero!');
			document.getElementById('qterecu').focus();
			return false;
           }

 	return true;
 }
