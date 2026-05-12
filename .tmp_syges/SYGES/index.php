<?php
	session_start();
	if (isset ($_SESSION['habilitation']))
	{
		
?>
<!DOCTYPE html>
<html dir="ltr" lang="en-US"><head><!-- Created by Artisteer v4.1.0.59861 -->
    <meta charset="utf-8">
    <title>SYGES 1.0</title>
    <meta name="viewport" content="initial-scale = 1.0, maximum-scale = 1.0, user-scalable = no, width = device-width">

    <!--[if lt IE 9]><script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <link rel="stylesheet" href="CSS/style.css" media="screen">
    <!--[if lte IE 7]><link rel="stylesheet" href="style.ie7.css" media="screen" /><![endif]-->
    <link rel="stylesheet" href="CSS/style.responsive.css" media="all">


    <script src="JS/jquery.js"></script>
    <script src="JS/script.js"></script>
    <script src="JS/script.responsive.js"></script>

<style>.art-content .art-postcontent-0 .layout-item-0 { border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:#9DC4D7; padding-right: 10px;padding-left: 10px;  }
.art-content .art-postcontent-0 .layout-item-1 { border-right-style:solid;border-right-width:1px;border-right-color:#9DC4D7; padding-right: 10px;padding-left: 10px;  }
.art-content .art-postcontent-0 .layout-item-2 { padding-right: 10px;padding-left: 10px;  }
.ie7 .art-post .art-layout-cell {border:none !important; padding:0 !important; }
.ie6 .art-post .art-layout-cell {border:none !important; padding:0 !important; }

label
{
	display:block;
	width:150px;
	float: left;
}
</style></head>
<body>
<div id="art-main">
<header class="art-header">

    <div class="art-shapes">

            </div>
<!--<h1  class="art-headline"  style="margin-top:20px; margin-left:800px">
 <label style="margin-left:180px">SYSTEME DE GESTION INFORMATISE</label>
</h1>-->
<label style="width:910px; text-align:right; margin-top:80px;">Utilisateur : <?php echo $_SESSION['login']?></label>
</header>
<nav class="art-nav">
    <?php 

					
		if(isset($_SESSION['habilitation']))
		{
			if($_SESSION['habilitation']=='Administrateur')
			{
				include('menu_Admin.php');
			}
			if	($_SESSION['habilitation']=='Gerant')
			{
				include('menu_Gerant.php');
			}
			if	($_SESSION['habilitation']=='Caissier')
			{
				include('menu_Caissier.php');
			}
			if	($_SESSION['habilitation']=='OPS')
			{
				include('menu_OPS.php');
			}
			if	($_SESSION['habilitation']=='Comptable')
			{
				include('menu_Comptable.php');
			}
			if	($_SESSION['habilitation']=='Magasinier')
			{
				include('menu_Magasinier.php');
			}
		}

	?>
    </nav>
<div class="art-sheet clearfix">
            <div class="art-layout-wrapper">
                <div class="art-content-layout">
                <div class="art-blockcontent">
                    <?php
                        if (isset($_REQUEST['formulaire'])) 
                        {
                            include($_REQUEST['formulaire'].".php");
                        }
						
						?>
               </div>
                </div>
            </div>
    </div>
<footer class="art-footer">
  <div class="art-footer-inner">

    <p class="art-page-footer">
        SYGES 1.0
                                Copyright &copy; 2020---. All Rights Reserved.
    </p>
  </div>
</footer>

</div>
</body></html>
<?php
	}
	else
						{
							include('Form_Connexion.php');
						}
?>
