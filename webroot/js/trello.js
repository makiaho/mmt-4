$(document).ready(function(){
    
    if($('#trello-data').length > 0){
    
    var boardId = $('#trello-data').data('board-id');
    var appKey = $('#trello-data').data('app-key');
    var token = $('#trello-data').data('token');
    
    var baseUrl = 'https://api.trello.com/1/boards';
    
    var url = baseUrl + '/' + boardId + '?fields=id,name&lists=open&list_fields=id,name';
    
    url += '&key=' + appKey;
    url += '&token=' + token;
    
    $.ajax({
        type: "GET",
        url: url,
        success: function (data) {

            var boardName = data.name;
            
            var text = 'Your board <b>' + boardName + '</b> has ' + data.lists.length + ' lists. You can link these lists to the four requirement types below.';

            var message = $('<div class="info-text">' + text + '</div>');

            $('.trello-form .form-message').append(message);

            for(var i = 0; i < data.lists.length; i++){
                
                var item = data.lists[i];
                
                var div = $('<div class="selectable trlist" data-id="' + item.id + '">' + item.name + '</div>');
                
                $('.trello-form form .trello-lists').append(div);
                
            }
            
            addRelations();
            $('.trello-form').removeClass('loading');

        },
        error: function (data) {
            
            var text = 'Could not get Trello lists. Please check your Trello configuration and try again';

            var message = $('<div class="info-text">' + text + '</div>');

            $('.trello-form .form-message').append(message);
            
            $('.trello-form form').remove();
            
            $('.trello-form').removeClass('loading');
        }
    });
    
    }else if ($('#trello-requirements').length > 0){
        
        var boardId = $('#trello-requirements').data('board-id');
        var appKey = $('#trello-requirements').data('app-key');
        var token = $('#trello-requirements').data('token');

        var baseUrl = 'https://api.trello.com/1/boards';

        var url = baseUrl + '/' + boardId + '/cards?fields=idList';

        url += '&key=' + appKey;
        url += '&token=' + token;
        
        $.ajax({
        type: "GET",
        url: url,
        success: function (data) {

            var trelloReqData = $('<div class="trello-req-numbers"></div>');
            
            $('#trello-requirements .trello-link').each(function(i){
                
                var listId = $(this).data('list-id');
                var reqName = $(this).data('req');
                
                var number = 0;
                
                for(var i = 0; i < data.length; i++){
                    
                    if(data[i].idList === listId){
                        number++;
                    }
                    
                }
                
                var div = $('<div class="req-number" data-req="' + reqName + '" data-number="' + number + '"></div>');
                
                trelloReqData.append(div);
                
            });
            
            $('#trello-requirements').append(trelloReqData);
            
            var messageText = 'You project has a valid trello connection. If you want, you can get the requirement numbers from trello lists.';
            
            var message = $('<div class="requirement-message">' + messageText + '</div>');
            
            message.append('<a href="#">Get Requirements</a>');
            
            $('#trello-requirements').append(message);
            

        },
        error: function (data) {
            
        }
    });
        
    }
    
});


$(document).on('click','.trello-req.current-select .selectable', function(){
    
    $(this).addClass('selected');
    
    $(this).closest('.select-list').removeClass('current-select');
    
    $('.trello-lists').addClass('current-select');
    
});


$(document).on('click','.trello-lists.current-select .selectable', function(){
    
    $(this).addClass('selected');
    
    $(this).closest('.select-list').removeClass('current-select');
    
    var match = $('<div class="match"></div>');
    
    match.append($('.trello-req .selected'));
    match.append($('.trello-lists .selected'));
    match.append('<div class="close">X</div>');
    
    var formData = $('<input type="hidden" name="' + match.find('.req').data('id') + '" value="' + match.find('.trlist').data('id') + '">');
    
    match.append(formData);
    
    $('.trello-matches').append(match);
    
    $('.trello-matches .match div').removeClass('selectable').removeClass('selected');
    
    $('.trello-req').addClass('current-select');
    
});

$(document).on('click','.trello-matches .match .close', function(){

    var container = $(this).closest('.match');
    
    var req = container.find('.req').addClass('selectable');
    var list = container.find('.trlist').addClass('selectable');
    
    $('.trello-req').append(req);
    $('.trello-lists').append(list);
    
    container.remove();

});


$(document).on('click','.requirement-message a', function(){
    
    $(this).closest('#trello-requirements').find('.req-number').each(function(i){
        
        $(this).closest('fieldset').find('input[name="' + $(this).data('req') + '"]').val($(this).data('number'));
        
    });
    
    return false;
    
});





function addRelations(){
    
    $('#saved-links .saved-link').each(function(i){
        
        var listId = $(this).data('list');
        var reqName = $(this).data('req');
        
        
        var reqItem = $('.trello-req').find('div[data-id="' + reqName + '"]');
        var listItem = $('.trello-lists').find('div[data-id="' + listId + '"]');
        
        var match = $('<div class="match"></div>');
    
        match.append(reqItem);
        match.append(listItem);
        match.append('<div class="close">X</div>');

        var formData = $('<input type="hidden" name="' + match.find('.req').data('id') + '" value="' + match.find('.trlist').data('id') + '">');

        match.append(formData);

        $('.trello-matches').append(match);
        
        $('.trello-matches .match div').removeClass('selectable').removeClass('selected');
        
    });
    
    
}