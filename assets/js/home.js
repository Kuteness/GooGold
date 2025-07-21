$(document).ready(function () {
   
    // stuff to declare first (just some method used and varibles)

	var protocol = window.location.protocol === 'https:' ? 'https://' : 'http://';
	var hostname = window.location.hostname;
	var port = window.location.port ? ':' + window.location.port : '';
	var siteUrl = protocol + hostname + port;


	// some thing I found off of w3 schools works well enough
    function GetCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i <ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
            c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function SetCookie(cname, cvalue, exdays) {
        let d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000)); 
            let expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + encodeURIComponent(cvalue) + ";" + expires + ";path=/";
    }

    function RemoveCookie(cname) {
        document.cookie = cname + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }


    // thing I found online
	var GetUrlParameter = function GetUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
			}
		}
		return false;
	};


    // end



    // stuff

    if(GetCookie("pp-loogle-instant-search")) {
        $("#loogle-instant-try").text("Disable Loogle Instant Search!");
    } else {
        console.log("yap");
        $("#loogle-instant-try").text("Try Loogle Instant Search!");
    }

    $("#loogle-instant-try").on("click", function(event) {

        if(GetCookie("pp-loogle-instant-search")) {
            RemoveCookie("pp-loogle-instant-search");
        } else {
            SetCookie("pp-loogle-instant-search", "enabled", "30")
        }

    });


    // end


});
