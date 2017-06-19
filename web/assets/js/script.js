
$(document).ready(function(){
	
	// expand
	$('body').on('click', '[data-expand]', function(e){
		e.preventDefault();
		
		if (typeof $(this).data('expanded') != 'undefined') {
			$('#comments_for_' + $(this).data('expand')).toggle();		// show / hide
			$(this).data('expanded', Math.abs($(this).data('expanded') - 1)).html( $(this).data('expanded') == 0 ? 'Развернуть &darr;' : 'Свернуть &uarr;' );		// 'data', link
			return;
		}
		
		$('#comments_for_' + $(this).data('expand')).html('<img src="/assets/images/preloader.gif" alt="" />');		// preload
		
		$.get('/ajax/comment', {parent_id: $(this).data('expand')}, function(r){
			$('#comments_for_' + r.parent_id).html(r.content);
			$('[data-expand="' + r.parent_id + '"]').data('expanded', 1).html('Свернуть &uarr;');
		}, 'json');
	});
	
	// add
	$('form').on('submit', function(e){
		e.preventDefault();
		
		var frm = $(this);
		
		$.post(frm.attr('action'), frm.serialize(), function(r){
			if (!parseInt(r.status)) {
				alert(r.message);
			} else {
				$('#comments_for_' + r.parent_id).append(r.comment_item);
				$('#comment_item_' + r.parent_id).addClass('children');
				$('[data-expand="' + r.parent_id + '"]').data('expanded', 1).trigger('click').trigger('click');
				frm.find('input, textarea').val('');
				var item = $('#comment_item_' + r.comment_id);
				$('html,body').animate({scrollTop: item.offset().top}, 500);
				item.removeClass('is_new');
			}
		}, 'json');
	});
	
	// delete
	$('body').on('click', '[data-remove]', function(e){
		e.preventDefault();
		if (!confirm('Действительно удалить комментарий?'))
			return;
		
		$.post('/ajax/comment/delete', {id: $(this).data('remove')}, function(r){
			if (parseInt(r.status)) {
				$('#comment_item_' + r.id).replaceWith(r.content);
			} else {
				alert('Ошибка удаления');
			}
		}, 'json');
	});
	
	// reply
	$('body').on('click', '[data-reply]', function(e){
		e.preventDefault();
		$('form').find('input, textarea').val('');
		$('#reply_to').val( $(this).data('reply') );
		$('form input:text:first-child').get(0).focus();
	});
	
	// edit
	$('body').on('click', '[data-edit]', function(e){
		e.preventDefault();
		$.get('/ajax/comment/edit', {id: $(this).data('edit')}, function(r){
			if (parseInt(r.status)) {
				$('#comment_item_' + r.id + ' .data p').hide().after(r.content);
			} else {
				alert('Ошибка редактирования');
			}
		}, 'json');
	});
	$('body').on('click', '[data-edit-cancel]', function(e){
		e.preventDefault();
		$(this).parents('.data').find('p').show();
		$(this).parents('form').eq(0).remove();
	});
	$('body').on('submit', '.tree .item form', function(e){
		e.preventDefault();
		
		var frm = $(this);
		
		$.post(frm.attr('action'), frm.serialize(), function(r){
			if (!parseInt(r.status)) {
				alert(r.message);
			} else {
				$('#comment_item_' + r.id + ' .data').html(r.content);
			}
		}, 'json');
	});
	
	// clean
	$('[data-clean]').on('click', function(e){
		e.preventDefault();
		$.get('/ajax/clean', function(r){
			if (parseInt(r.status))
				location.reload();
		}, 'json');
	});
	
})