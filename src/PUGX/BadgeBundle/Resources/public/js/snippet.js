function _Redirect (url) {
    var ua        = navigator.userAgent.toLowerCase(),
        verOffset = ua.indexOf('msie') !== -1,
        version   = parseInt(ua.substr(4, 2), 10);

    // IE8 and lower
    if (verOffset && version < 9) {
        var link = document.createElement('a');
        link.href = url;
        document.body.appendChild(link);
        link.click();
    }

    // All other browsers
    else { window.location.href = url; }
}

function bsalert (text) {
    $('#alertModal .modal-contents').html(text);
    $('#alertModal').modal('show');
}

$(document).ready(function(){

    $("#repository").select2({
        placeholder: "vendor/repository",
        minimumInputLength: 3,
        ajax: {
            url: "./search_packagist",
            dataType: 'json',
            data: function (term, page) {
                return {
                    name: term
                };
            },
            results: function (data, page) {
                return {results: data};
            }
        },
        formatResult: packageFormatResult,
        formatSelection: packageFormatSelection,
        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) { return m; }
    });

    function packageFormatResult(packageInfo) {
        var markup = "<dt>" + packageInfo.id + "</dt><dd>" + packageInfo.description + "</dd>";
        return markup;
    }

    function packageFormatSelection(packageInfo) {
        return packageInfo.id;
    }

    var lock = function(){
      $('#spinningSquaresG').show();
        $('.prettyprint').each(function(idx, el){
            $(el).addClass('grey-text');
        });
        $('.spinned').each(function(idx, el){
            $(el).fadeOut();
        });
    };

    var unlock = function(){
        $('#spinningSquaresG').hide();
        setTimeout(function(){
            $('.prettyprint').each(function(idx, el){
                $(el).removeClass('grey-text');
            });
            $('.spinned').each(function(idx, el){
                $(el).fadeIn(1500);
            });
        },200);

    };

    var generateSnippets = function(snippets_raw_data){
        $.each(snippets_raw_data, function(idx, snippet) {

		if (idx == 'repository') {
			$('#' + idx + '_html').html(snippet.html).append('&nbsp;<a class="headerlink" href="show/'+snippet.html+'#badges" title="Permalink for '+snippet.html+' repository">¶</a>')
			return true;
		}

		if (idx != 'clip_all') {
			$('.' + idx + '_img').attr('src', snippet.img);
		}

                $('#' + idx + '_markdown').attr('value', snippet.markdown);

        });
    };

    $("#repository").on('change', function(){

        lock();

        $.ajax({
                url: $('#generate-form').attr('action'),
                data: $('#generate-form').serialize(),
                success: function(data){
                    generateSnippets(data);
                    unlock();
                },
                statusCode: {
                    404: function() {
                        bsalert("Repository not found!");
                        unlock();
                    },
                    500: function() {
                        bsalert("Repository not found!");
                        unlock();
                    }
                }
        });

        return false;

    });

});
