<!DOCTYPE html>
<html>

<head>
	<title>GooGold</title>

	<meta name="description" content="Search the web for sites and images.">
	<meta name="keywords" content="search engine, doogle, websites">
	<meta name="author" content="Zepher Ashe, NCP3.0">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="icon" type="image/x-icon" href="assets/images/favicon/favicon.ico">
	<link rel="shortcut icon" type="image/png" href="assets/images/favicon/favicon-32x32.png">
	<link rel="apple-touch-icon" href="assets/images/favicon/apple-touch-icon.png">
	<link rel="android-chrome-icon" type="image/png" href="assets/images/favicon/android-chrome-512x512.png">

	<link rel="stylesheet" type="text/css" href="assets/css/homepage.css">

	<script src="/assets/js/jquery-3.7.1.min.js"></script>
	<script src="/assets/js/config.js"></script>
	<script src="/assets/js/home.js"></script>


</head>

<body bgcolor="#fff">
	<center>
		<br clear="all" id="lgpd" />
		<div id="lga">
			<img alt="Google" height="95"
				src="/assets/site_assets/logo.png" width="355"
				id="hplogo" onload="window.lol&amp;&amp;lol()" style="padding: 28px 0 14px;" /><br />
			<br />
		</div>
		<form action="search.php" name="f">
			<table cellpadding="0" cellspacing="0">
				<tbody>
					<tr valign="top">
						<td width="25%">&nbsp;</td>
						<td align="center" nowrap="nowrap">
							<input value="en" name="hl" type="hidden" /><input name="source" type="hidden" value="hp" />
							<div class="ds" style="height: 32px; margin: 4px 0;">
								<input autocomplete="off" class="lst" value="" title="GooGold Search" maxlength="2048"
									name="q" size="57"
									style="color: rgb(0, 0, 0); margin: 0px; padding: 5px 8px 0px 6px; vertical-align: top; outline: none;"
									dir="ltr" spellcheck="false" />
							</div>
							<br style="line-height: 0;" />
							<span class="ds">
								<span class="lsbb"><input class="lsb" value="GooGold Search" name="btnG"
										type="submit" /></span>
							</span>
							<span class="ds">
								<span class="lsbb">
								<input 
									class="lsb" 
									value="I'm Feeling Lucky" 
									name="btnI"
									type="submit"
									onclick="window.location.href='/api/v1/feeling_lucky.php'; return false;"
								/>
								</span>
							</span>
						</td>
						<td class="fl sblc" align="left" nowrap="nowrap" width="25%">

						</td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" id="gbv" name="gbv" value="2" />
			<script>
				(function () {
					var a,
						b = "1";
					if (document && document.getElementById)
						if ("undefined" != typeof XMLHttpRequest) b = "2";
						else if ("undefined" != typeof ActiveXObject) {
							var c,
								d,
								e = ["MSXML2.XMLHTTP.6.0", "MSXML2.XMLHTTP.3.0", "MSXML2.XMLHTTP", "Microsoft.XMLHTTP"];
							for (c = 0; (d = e[c++]);)
								try {
									new ActiveXObject(d), (b = "2");
								} catch (f) { }
						}
					a = b;
					"2" == a && (document.getElementById("gbv").value = a);
				})();
			</script>
			<input type="hidden" name="oq" /><input type="hidden" name="gs_l" />
		</form>
		<div id="gac_scont"></div>
		<div style="font-size: 83%; min-height: 3.5em;"><br /></div>
		<span id="footer">
			<div style="font-size: 10pt;">
				<div id="fll" style="margin: 19px auto; text-align: center;">
					<a href="http://igoogold.atspace.co.uk" rel="publisher">iGooGold (only in french for the moment and in dev)</a>
					<a href="submit_site.php">Submit A Site!</a>
					<a href="#">About GooGold</a>
				</div>
			</div>
			<p style="color: #767676; font-size: 8pt;">© 2011 - <a
					href="http://staging.loogle.cc/accounts/privacy_policy.php/">Privacy &amp; Terms</a></p>
            			<p style="color: #767676; font-size: 8pt;">Powered by Loogle Search Engine</p>
		</span>
	</center>
</body>

</html>