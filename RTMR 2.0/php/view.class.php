<?php
define('APPNAME', "Rat Town Movie Reviews");
define('APPLOGO', 'assets\RTMRT.png'); 




	/**************
	* This class is used to easily place styles and other metadata on pages without having to type each individual item
	**************/
class view 
{
    public static function showHead($pagename) 
    {
?>
        <html lang="en-GB" dir="ltr">

        <head>
            <?php

            ?>
            <meta charset="UTF-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
            <link rel="stylesheet" type="text/css" href="styles\style.css" />
            <!-- <link rel="stylesheet" type="text/css" media="screen and (max-width:639px)" href="" />
            <link rel="stylesheet" type="text/css" media="screen and (min-width:640px)" href="" /> -->
            <!-- <link rel="stylesheet" type="text/css" media="print" href="../css/print.css" /> -->
            <link rel="icon" type="image/x-icon" href="assets/ratticusgood.ico">
            <title><?php echo $pagename . " - " . APPNAME; ?></title>
    </head>

    <?php
    }


    public static function showHeader($pagename)
    {
        ?>
        <header id='mainheader' class = "mainbar">
            <h1 class = "maintitle">
                <img class = "maintitleimg" src="<?php echo APPLOGO; ?>" alt="<?php echo APPNAME; ?>" style="height: 100px;">
                <!-- <?php echo APPNAME; ?> -->
                
            </h1>
        </header>
        <?php
    }

}