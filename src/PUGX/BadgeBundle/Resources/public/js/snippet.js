
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

    function packageFormatResult(package) {
        var markup = "<dt>" + package.id + "</dt><dd>" + package.description + "</dd>";
        return markup;
    }

    function packageFormatSelection(package) {
        return package.id;
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
            $.each(snippets_raw_data, function(idx, snippet){
                    $('#' + idx + '_markdown').html(snippet.markdown);
                    $('#' + idx + '_img').attr('src', snippet.img);
            });
    };

    $('#generate').click(function(){

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
                        alert("Repository not found!");
                        unlock();
                    },
                    500: function() {
                        alert("Repository not found!");
                        unlock();
                    }
                }
        });
    
        return false;
    
    });

});