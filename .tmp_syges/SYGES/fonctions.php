<?php
// retourne la date au format aaaa-mm-jj
	function dateFormatAnglais($datefr)
	{
		$jr=substr($datefr,0,2)	;
		$mois=substr($datefr,3,2);
		$an=substr($datefr,6,4)	;
		$datean=$an.'-'.$mois.'-'.$jr;
	return $datean;
	}
	// retourne la date au format aaaa-mm-jj
	function dateFormatFrancais($datean)
	{
		$jr=substr($datean,8,2)	;
		$mois=substr($datean,5,2);
		$an=substr($datean,0,4)	;
		$datefr=$jr.'/'.$mois.'/'.$an;
		return $datefr;
	}
	// retourne la date au format aaaammjj pour ccomparaison
	function concat_date($date)
	{
		$jr=substr($date,8,2);
		$mois=substr($date,5,2);
		$an=substr($date,0,4);
		$concat=$an.$mois.$jr;
		return $concat;
	}
	// Génére le code Inv
		function generer_code_inv()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_INV,10)) AS NRO FROM INVENTAIRE';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<3;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE ARTICLE
		$nom=date('dmY');
		return $nom.'-'.$nb_zero.$nro;
	}
	// Génére le code de l'article
		function generer_code_article()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_ARTICLE,4)) AS NRO FROM ARTICLE';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<5;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE ARTICLE
		$nom='ART';
		return $nom.$nb_zero.$nro;
	}
		// Génére le code de l'EMBALLAGE
		function generer_code_emb()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_EMBALLAGE,4)) AS NRO FROM EMBALLAGE';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<5;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE ARTICLE
		$nom='EMB';
		return $nom.$nb_zero.$nro;
	}
		// Génére le code de la charge
		function generer_code_charge()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_CHARGE,4)) AS NRO FROM CHARGE';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<5;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE CHARGE
		$nom='CHG';
		return $nom.$nb_zero.$nro;
	}
	// Génére le code VENTE
	function generer_code_vente()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_SORTIESTOCK,4)) AS NRO FROM SORTIE_STOCK';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<7;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE VENTE
		$nom='VTE';
		return $nom.$nb_zero.$nro;
	}
		function generer_code_ventefrigo()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_SORTIESTOCK,4)) AS NRO FROM SORTIE_STOCK_FRIGO';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<7;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE VENTE
		$nom='VTF';
		return $nom.$nb_zero.$nro;
	}
	
			function generer_code_perte()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_SORTIESTOCK,4)) AS NRO FROM SORTIE_STOCK_FRIGO';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<7;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE VENTE
		$nom='PER';
		return $nom.$nb_zero.$nro;
	}
	
			function generer_code_sortiecession()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_SORTIESTOCK,4)) AS NRO FROM SORTIE_STOCK_CESSION';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<7;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE VENTE
		$nom='STC';
		return $nom.$nb_zero.$nro;
	}
		// Génére le code de l'apro emb
		function generer_code_appro_emb()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_APPRO,4)) AS NRO FROM APPROEMB';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<5;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE APPRO
		$nom='APE';
		return $nom.$nb_zero.$nro;
	}
			// Génére le code de l'apro magasin
		function generer_code_appro()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_APPRO,4)) AS NRO FROM APPROVISIONNEMENT';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<5;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE APPRO
		$nom='APP';
		return $nom.$nb_zero.$nro;
	}
		function generer_code_approcession()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_APPRO,4)) AS NRO FROM APPROCESSION';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<5;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE APPRO
		$nom='APC';
		return $nom.$nb_zero.$nro;
	}
			function generer_code_appfrigo()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_APPRO,4)) AS NRO FROM APPROFRIGO';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<5;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE APPRO
		$nom='APF';
		return $nom.$nb_zero.$nro;
	}
			function generer_code_fournisseur()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_FOURNISSEUR,4)) AS NRO FROM FOURNISSEUR';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<5;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE FOURNISSEUR
		$nom='FSR';
		return $nom.$nb_zero.$nro;
	}
		// Génére le code du client
		function generer_code_client()
	{
		
		include('connexion.php');
		$sql='SELECT MAX(SUBSTR(ID_CLIENT,4)) AS NRO FROM CLIENT';	
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		$nro= $rslt['NRO'];
		$nro= $nro+1;
		$nb_chiffre= strlen($nro);
		$nb_zero='';
		for($i=$nb_chiffre;$i<5;$i++)
		{
			$nb_zero=$nb_zero.'0';
		}
		// INSERTION CODE CLIENT
		$nom='CLT';
		return $nom.$nb_zero.$nro;
	}

//convertion des chiffres en lettres
function asLetters($number) 
{
    $convert = explode('.', $number);
    $num[17] = array('Zero', 'Un', 'Deux', 'Trois', 'Quatre', 'Cinq', 'Six', 'Sept', 'Huit',
                     'Neuf', 'Dix', 'Onze', 'Douze', 'Treize', 'Quatorze', 'Quinze', 'Seize');
                      
    $num[100] = array(20 => 'Vingt', 30 => 'Trente', 40 => 'Quarante', 50 => 'Cinquante',
                      60 => 'Soixante', 70 => 'Soixante-dix', 80 => 'Quatre-vingt', 90 => 'Quatre-vingt-dix');
                                      
    if (isset($convert[1]) && $convert[1] != '') {
      return asLetters($convert[0]).' et '.asLetters($convert[1]);
    }
    if ($number < 0) 
		return 'moins '.asLetters(-$number);
    if ($number < 17) 
	{
      return $num[17][$number];
    }
    elseif ($number < 20) 
	{
      return 'Dix-'.asLetters($number-10);
    }
    elseif ($number < 100) 
	{
      if ($number%10 == 0) 
	  {
        return $num[100][$number];
      }
      elseif (substr($number, -1) == 1) 
	  {
        if( ((int)($number/10)*10)<70 )
		{
          return asLetters((int)($number/10)*10).'-et-un';
        }
        elseif ($number == 71) 
		{
          return 'Soixante-et-onze';
        }
        elseif ($number == 81) 
		{
          return 'Quatre-vingt-un';
        }
        elseif ($number == 91) 
		{
          return 'Quatre-vingt-onze';
        }
      }
      elseif ($number < 70) 
	  {
        return asLetters($number-$number%10).'-'.asLetters($number%10);
      }
      elseif ($number < 80) 
	  {
        return asLetters(60).'-'.asLetters($number%20);
      }
      else 
	  {
        return asLetters(80).'-'.asLetters($number%20);
      }
    }
    elseif ($number == 100) 
	{
      return 'Cent';
    }
    elseif ($number < 200) 
	{
      return asLetters(100).' '.asLetters($number%100);
    }
    elseif ($number < 1000) 
	{
      return asLetters((int)($number/100)).' '.asLetters(100).($number%100 > 0 ? ' '.asLetters($number%100): '');
    }
    elseif ($number == 1000)
	{
      return 'Mille';
    }
    elseif ($number < 2000) 
	{
      return asLetters(1000).' '.asLetters($number%1000).' ';
    }
    elseif ($number < 1000000) 
	{
      return asLetters((int)($number/1000)).' '.asLetters(1000).($number%1000 > 0 ? ' '.asLetters($number%1000): '');
    }
    elseif ($number == 1000000) 
	{
      return ' Un Million';
    }
    elseif ($number < 2000000) 
	{
      return asLetters(1000000).' '.asLetters($number%1000000);
    }
    elseif ($number < 1000000000) 
	{
      return asLetters((int)($number/1000000)).' '.'Millions'.($number%1000000 > 0 ? ' '.asLetters($number%1000000): '');
    }
  }
?>