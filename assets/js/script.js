$ = new jQuery.noConflict()
$(document).ready(function(){
	$('#select-edit-next').select2().change(function(e){
		var chosen = $(this).val()
		if( chosen != '' ){
			var go_to = edit_post_url + '?post=' + chosen + '&action=edit';
			window.location.href = go_to
		}
	})
})