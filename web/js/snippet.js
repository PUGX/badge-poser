$(document).ready(function(){

	var generateSnippets = function(snippets_raw_data){
		$.each(snippets_raw_data, function(idx, snippet){
			$('#' + idx + '_markdown').html(snippet.markdown);
			$('#' + idx + '_img').attr('src', snippet.img);
		});
	};

	$('#generate').click(function(){
		$.ajax({
			url: $('#generate-form').attr('action'),
			data: $('#generate-form').serialize(),
			success: function(data){
				generateSnippets(data);
			},
		});
		return false;
	});

});