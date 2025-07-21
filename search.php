<?php
if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

include './config.php';

?>

<html itemscope="itemscope" itemtype="http://schema.org/WebPage" style="--wm-toolbar-height: 1px;" data-lt-installed="true">
   <head>

   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <meta name="description" content="Search the web for sites and images.">
   <meta name="keywords" content="search engine, doogle, websites">
   <meta name="author" content="Zepher Ashe, NCP3.0">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <link rel="icon" type="image/x-icon" href="assets/images/favicon/favicon.ico">
   <link rel="shortcut icon" type="image/png" href="assets/images/favicon/favicon-32x32.png">
   <link rel="apple-touch-icon" href="assets/images/favicon/apple-touch-icon.png">
   <link rel="android-chrome-icon" type="image/png" href="assets/images/favicon/android-chrome-512x512.png">

	<link rel="stylesheet" type="text/css" href="assets/css/search.css">

    <script src="/assets/js/jquery-3.7.1.min.js"></script>
	<script src="/assets/js/config.js"></script>
	<script src="/assets/js/search.js"></script>

   </head>	
   

   <body marginheight="0" topmargin="0" bgcolor="#ffffff" marginwidth="0">
      <div></div>
      <table border="0" cellpadding="0" cellspacing="0" id="mn" style="position:relative">
         <tbody>
            <tr>
               <th width="132"></th>
               <th width="573"></th>
               <th width="278"></th>
               <th></th>
            </tr>
            <tr>
               <td class="sfbgg" valign="top">
                  <a href="/">
                     <div id="logocont">
                        <h1>
                     <div class="logo-con">
                        <img src="/assets/site_assets/logo.png" href="<?php echo $SITE_URL ?>" id="logo" title="Go to GooGold Home"></img>
                     </div>
                  </a>
					</h1>
                  </div>
               </td>
               <td class="sfbgg" valign="top" colspan="2" style="padding-left:8px">
                  <form action="<?php echo $SITE_URL?>/search.php" name="gs" id="tsf" method="GET" style="display:block;margin:0;background:none">
                     <table border="0" cellpadding="0" cellspacing="0" style="margin-top:20px;position:relative">
                        <tbody>
                           <tr>
                              <td>
                                 <div class="lst-a">
                                    <table cellpadding="0" cellspacing="0">
                                       <tbody>
                                          <tr>
                                             <td class="lst-td" width="555" valign="bottom">
                                                <div style="position:relative;zoom:1">
                                                   <input class="search-input" value="" title="Search" id="sbhost" autocomplete="off" type="text" name="q" maxlength="2048" dir="ltr" spellcheck="false" style="outline: none;">
                                                </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </td>
                              <td>
                                 <div class="ds">
                                    <div class="lsbb">
										<button class="lsb" value="Search" type="submit" name="btnG"><span class="search-icon"></span></button>
									</div>
                                 </div>
                              </td>
                              <td style="font-size:11px;padding-left:13px"></td>
                           </tr>
                        </tbody>
                     </table>
                     <input type="hidden" name="oq"><input type="hidden" name="gs_l">
                  </form>
               </td>
               <td class="sfbgg">&nbsp;</td>
            </tr>
            <tr>
               <td class="ab_bg">
                  <div id="ab_name"><span>Search</span></div>
               </td>
               <td class="ab_bg" colspan="2">
                  <div id="subform_ctrl">
                     <div id="resultStats"></div>
                  </div>
               </td>
               <td class="ab_bg">&nbsp;</td>
            </tr>
            <tr>
               <td valign="top" id="leftnav" style="padding:22px 4px 4px 0px">
                  <div id="modeselector" style="padding-bottom:4px">
                     <ul>
                        <li class="mitem msel">Web</li>
                        <li class="mitem"><a class="q" href="">Images</a></li>
                        <li class="mitem"><a class="q" href="">News</a></li>
                        <a href="#" style="display:none"></a>
                        <a href="#" style="display:none"></a>
                     </ul>
                  </div>
                  <div class="lnsec"></div>
                  <div>
                     <h2 class="hd">Search Options</h2>
                     <ul class="med" id="tbd">
                        <li>
                           <ul class="tbt" style="display:none"></ul>
                        </li>
                        <li>
                           <ul class="tbt" style="display:none"></ul>
                        </li>
                     </ul>
                  </div>
               </td>
               <td valign="top">
                  <div id="center_col">
                     <div id="res">
                        <div id="topstuff"></div>
                        <div id="search">
                           <div id="results-container">
                  
                           </div>
                        </div>
                     </div>
                     <div style="clear:both;margin-bottom:17px;overflow:hidden">
                        <table id="related-searches-container" border="0" cellpadding="0" cellspacing="0">
                           <tbody>

                           </tbody>
                        </table>
                     </div>
                  </div>
                  <div id="foot">
                     <table align="center" border="0" cellpadding="0" cellspacing="0" id="nav">
                        <tbody>
                           <tr valign="top">
                              <td class="b" align="left"><span class="csb" style="background-position:-24px 0;width:28px"></span><b></b></td>
                     
                              <td
                                 class="b" style="text-align:left"><a href="#">Next</span></a></td>
                           </tr>
                        </tbody>
                     </table>
                     <p class="flc" id="bfl" style="margin:19px 0 0;text-align:center"><a href="#">Send feedback</a></p>
                     <div class="flc" id="fll" style="margin:19px auto 19px auto;text-align:center">
                        <a href="index.php">GooGold&nbsp;Home</a> 
                        <a href="submit_site.php">Submit Site</a>          
                        <a href="http://googold.atspace.co.uk/account/privacy_policy.php">Privacy &amp; Terms</a>
                        <a href="#">About GooGold</a></div>
                  </div>
               </td>
               <td valign="top"></td>  
            </tr>
         </tbody>
      </table>
   </body>


</html>
