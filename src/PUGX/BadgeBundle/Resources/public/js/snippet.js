$(document).ready(function(){

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
});