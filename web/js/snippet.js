$(document).ready(function(){

    var lock = function(){
      $('#spinningSquaresG').show();
      $('#overlay').fadeIn();
    };

    var unlock = function(){
        $('#spinningSquaresG').hide();
        $('#overlay').fadeOut(500);
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