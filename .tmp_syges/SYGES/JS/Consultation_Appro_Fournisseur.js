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
function concat_date(input)
{
var regex = new RegExp("[/-]");
var date = input.split(regex);
var result=0;
result= date['2']+ date['1']+ date['0'];
return result;
}

 function verif_form()
 {
  if(document.getElementById('codefssr').value=="")
           {
            alert('BV choisir le fournisseur.');
			document.getElementById('codefssr').focus();
			return false;
           }

   if(!verif_date(document.getElementById('DateD').value))
           {
            alert('Date de debut de la période incorrect.');
			document.getElementById('DateD').focus();
			return false;
           }

   if(!verif_date(document.getElementById('DateF').value))
           {
            alert('Date de fin de la période incorrect.');
			document.getElementById('DateF').focus();
			return false;
           }

if (concat_date(document.getElementById('DateD').value) > concat_date(document.getElementById('DateF').value))
	{
			alert('Date de fin antérieure à la date de début.');
			document.getElementById('DateF').focus();
			return false;
	}
	return true;
 }// JavaScript Document