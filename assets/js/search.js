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
        for (let i = 0; i < ca.length; i++) {
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


    var query = GetUrlParameter("q");
    var searchType = "web";

    Search();

    // end

    // search result stuff 

    // stuff that don't need no method

    document.title = query + " - Loogle Search";

    $(".search-input").attr("value", query);

    // end

    $(".search-input").val(query); // Set input value

    // tab switching

    $("#modeselector li.mitem").on("click", "a.q", function (e) {
        e.preventDefault();

        var clickedText = $(this).text().toLowerCase();

        if (clickedText === "web" || clickedText === "images" || clickedText === "news") {
            searchType = clickedText;

            $("#modeselector li.mitem").each(function () {
                var $li = $(this);
                var label = $li.text().trim().toLowerCase();

                if (label === searchType) {
                    $li.html(label.charAt(0).toUpperCase() + label.slice(1));
                    $li.addClass("msel");
                } else {
                    var otherType = $li.text().trim();
                    $li.html(`<a class="q" href="#">${otherType}</a>`);
                    $li.removeClass("msel");
                }
            });

            Search(0);
        }
    });

    // end


    function Search(start = 0) {
        const startTime = performance.now();
        var pageSize = 20;
        var page = Math.floor(start / pageSize) + 1;

        var apiUrl = siteUrl + '/api/v1/search_results.php';
        var dataParams = {
            q: query,
            page: page,
            pageSize: pageSize,
            type: searchType
        };

        $.ajax({
            url: apiUrl,
            type: 'GET',
            data: dataParams,
            success: function (response) {
                const duration = ((performance.now() - startTime) / 1000).toFixed(2);

                if (response.status === 'success') {
                    var results = response.totalResults;
                    var items = response.results;

                    $("#resultStats").text("About " + results + " results (" + duration + " seconds)");
                    $("#results-container").empty();

                    if (searchType === 'web') {
                        items.forEach(function (item) {
                            const safeTitle = $('<div>').text(item.title).html();
                            const safeDesc = $('<div>').text(item.description).html();
                            const safeUrl = $('<div>').text(item.url).html();

                            const html = `
                                <li class="g">
                                    <h3 class="r">
                                        <a href="${safeUrl}" target="_blank" data-id="${item.id}" data-type="web" class="result-link">
                                            <b>${safeTitle}</b>
                                        </a>
                                    </h3>
                                    <div class="s">
                                        <div class="kv" style="margin-bottom:2px">
                                            <cite>${new URL(item.url).hostname}</cite>
                                        </div>
                                        <span class="st">${safeDesc}</span><br>
                                    </div>
                                </li>`;
                            $("#results-container").append(html);
                        });
                    }
                    
                    else if (searchType === 'news') {
                        items.forEach(function (item) {
                            const safeTitle = $('<div>').text(item.title).html();
                            const safeDesc = $('<div>').text(item.description).html();
                            const safeUrl = $('<div>').text(item.url).html();

                            const safeImage = item.imageUrl
                                ? `<td style="width: 80px; vertical-align: top; padding-right: 8px;">
                                        <img src="${item.imageUrl}" style="width: 72px; height: 72px; object-fit: cover; border: 1px solid #ddd;" />
                                </td>`
                                : `<td style="width: 80px;"></td>`;

                            const html = `
                                <li class="g" style="margin-bottom: 12px;">
                                    <table style="width: auto; border-spacing: 0;">
                                        <tr>
                                            ${safeImage}
                                            <td style="vertical-align: top; max-width: 600px;">
                                                <h3 class="r" style="margin: 0 0 4px 0;">
                                                    <a href="${safeUrl}" target="_blank" data-id="${item.id}" data-type="news" class="result-link" style="text-decoration:none; color:#1a0dab;">
                                                        <b>${safeTitle}</b>
                                                    </a>
                                                </h3>
                                                <div class="kv" style="margin-bottom: 2px; color: #006621; font-size: 12px;">
                                                   <cite>${new URL(item.url).hostname}</cite> - <span style="color: #545454;">${item.publishedDate}</span>
                                                </div>
                                                <span class="st" style="font-size: 14px; color: #545454;">${safeDesc}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </li>`;

                            $("#results-container").append(html);
                        });
                    }
                    else if (searchType === 'images') {
                        items.forEach(function (item) {

                            const safeTitle = $('<div>').text(item.title).html();
                            const safeSource = $('<div>').text(item.siteUrl).html();

                            const html = `
                                <li class="image-result" style="width: 150px; height: 150px; display:inline-block; margin:10px;">
                                    <a href="${safeSource}" target="_blank" title="${safeTitle}" data-id="${item.id}" data-type="images" class="result-link">
                                        <img src="${item.imageUrl}" alt="${safeTitle}" 
                                            style="width:100%; height:100%; display:block; object-fit:fill;" 
                                            onerror="this.parentElement.parentElement.style.display='none';" />
                                        <span style="display:block; width:100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            ${safeTitle}
                                        </span>
                                    </a>
                                </li>`;
                            
                            $("#results-container").append(html);
                        });
                    }



                    RenderPagination(start, pageSize, results);

                    if (searchType === 'web') {
                        CreateRelatedSearches(query.split(" ")[0], ["images", "earth", "email", "voice", "mail", "scholar", "books", "finance"]);
                    } else {
                        $("#related-searches-container").empty();
                    }
                } else {
                    $("#results-container").html("<p>No results found.</p>");
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    }


    if (query && query.trim() !== "") {
        Search(0);
    }

    function RenderPagination(start, perPage, total) {
        const currentPage = Math.floor(start / perPage) + 1;
        const totalPages = Math.ceil(total / perPage);
        const maxPages = 10;

        const $nav = $('#nav');
        $nav.empty();

        const $row = $('<tr valign="top"></tr>');

        // back button
        if (currentPage > 1) {
            const backStart = (currentPage - 2) * perPage;
            const $backTd = $('<td class="b" align="left"></td>').append(
                $('<a href="#">')
                    .on('click', function (e) {
                        e.preventDefault();
                        Search(backStart);
                    })
                    .append('<span class="csb" style="background-position:-24px 0;width:28px"></span>')
            );
            $row.append($backTd);
        } else {
            $row.append('<td class="b" align="left"><span class="csb" style="background-position:-24px 0;width:28px"></span></td>');
        }

        // page links
        for (let i = 1; i <= totalPages && i <= maxPages; i++) {
            const pageStart = (i - 1) * perPage;
            const $td = $('<td></td>');
            if (i === currentPage) {
                $td.html('<b>' + i + '</b><span class="csb" style="background-position:-53px 0;width:20px"></span>');
            } else {
                const $a = $('<a href="#" class="fl"></a>')
                    .text(i)
                    .on('click', function (e) {
                        e.preventDefault();
                        Search(pageStart);
                    });
                $td.append('<span class="csb" style="background-position:-74px 0;width:20px"></span>').append($a);
            }
            $row.append($td);
        }

        // next button
        if (currentPage < totalPages) {
            const nextStart = currentPage * perPage;
            const $nextTd = $('<td class="b" style="text-align:left"></td>').append(
                $('<a href="#">')
                    .on('click', function (e) {
                        e.preventDefault();
                        Search(nextStart);
                    })
                    .append('<span class="csb" style="background-position:-96px 0;width:71px"></span><span style="display:block;margin-left:53px">Next</span>')
            );
            $row.append($nextTd);
        }

        $nav.append($('<tbody></tbody>').append($row));
    }

    function CreateRelatedSearches(baseTerm, relatedTerms) {
        var $container = $("#related-searches-container");
        if ($container.length === 0 || !relatedTerms || relatedTerms.length === 0) return;

        var $wrapper = $('<div style="clear:both;margin-bottom:17px;overflow:hidden"></div>');
        var $header = $('<div style="font-size:16px;padding:0 8px 1px">Searches related to <b>' + baseTerm + '</b></div>');
        var $table = $('<table border="0" cellpadding="0" cellspacing="0"><tbody></tbody></table>');
        var $tbody = $table.find('tbody');

        for (var i = 0; i < relatedTerms.length; i += 2) {
            var $row = $('<tr></tr>');

            for (var j = 0; j < 2; j++) {
                var term = relatedTerms[i + j];
                if (!term) break;

                var $cell = $('<td valign="top"></td>');
                if (j === 1) $cell.css('padding-left', '10px');

                var $link = $('<a></a>')
                    .attr('href', '?q=' + encodeURIComponent(baseTerm + ' ' + term))
                    .html(baseTerm + ' <b>' + term + '</b>');

                var $p = $('<p class="msrl" style="margin:3px 8px"></p>').append($link);
                $cell.append($p);
                $row.append($cell);
            }

            $tbody.append($row);
        }

        $wrapper.append($header).append($table);
        $container.empty().append($wrapper);
    }


    if (GetCookie("pp-loogle-instant-search")) {

        var typingTimer;
        var doneTypingInterval = 300;

        $(".search-input").on('input', function () {
            clearTimeout(typingTimer);
            var newQuery = $(this).val().trim();

            typingTimer = setTimeout(function () {
                if (newQuery.length > 0) {
                    query = newQuery;
                    Search();
                } else {
                    $("#results-container").empty();
                    $("#resultStats").text("");
                }
            }, doneTypingInterval);
        });

        var urlQuery = GetUrlParameter("q");
        var pageParam = GetUrlParameter("page");
        var pageSize = 20;
        var start = 0;

        if (pageParam && !isNaN(pageParam)) {
            start = (parseInt(pageParam, 10) - 1) * pageSize;
        }

        if (urlQuery && urlQuery.trim() !== "") {
            query = urlQuery;
            $(".search-input").val(query);
            Search(start);
        }
    }


    // end

    // click tracker

    $(document).on("click", ".result-link", function () {

        var resultId = $(this).data("id");
        var resultType = "web"

        $.ajax({
            url: "/api/v1/track_click.php",
            method: "GET",
            data: {
                id: resultId,
                type: resultType
            },
            success: function (res) {
                console.log("Click tracked:", res);
            },
            error: function (xhr, status, err) {
                console.error("Click tracking failed:", err);
            }
        });
    });


    $(".image-result a").click(function () {

        var resultId = $(this).data("id");
        var resultType = "images"

        $.ajax({
            url: "/api/v1/track_click.php",
            method: "GET",
            data: {
                id: resultId,
                type: resultType
            },
            success: function (response) {
                console.log("Click tracked:", response);
            },
            error: function (xhr, status, error) {
                console.error("Failed to track click:", error);
            }
        });
    });


    // end


});
