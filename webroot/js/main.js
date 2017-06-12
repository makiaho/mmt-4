// Opens and closes dropdown menu
$(document).on('click','.dropdown button',function(){
    
    $(this).closest('.dropdown').toggleClass('on');
    
    
});


// Closes the dropdown menu when clicked outside
$(document).mouseup(function (e) {

    if ($('.dropdown.on').length > 0) {
        var container = $('.dropdown.on');

        if ((!container.is(e.target) && container.has(e.target).length === 0)) {

            container.removeClass('on');
        }
    }
});


// Opening and closing edit mode for comments
$(document).on('click','.messagebox .msgaction a.edit',function(){
    
    var container = $(this).closest('.messagebox');
    var contentBox = container.find('.msg-content');
    
    var text = contentBox.find('span').text();
    
    var path = container.find('.msgaction').data('edit-url');
    
    var editForm = $('<form></form>');
    
    editForm.attr('method', 'post');
    editForm.attr('action', path);
    
    var textarea = $('<textarea name="content">' + text + '</textarea>');
    
    container.addClass('edit-mode');
    editForm.append(textarea);
    contentBox.append(editForm);
    
    return false;       
});

$(document).on('click','.messagebox .msgaction a.cancel',function(){

    var container = $(this).closest('.messagebox');
    var contentBox = container.find('.msg-content');

    contentBox.find('form').remove();
    container.removeClass('edit-mode');

    return false;    
});


// Send the edit form an clicking save link
$(document).on('click','.messagebox .msgaction a.save',function(){
    
    $(this).closest('.messagebox').find('form').submit();
    
    return false;
});


$(document).on('onmousemove', 'body',function(){
    $('.message').delay(2500).fadeOut(1000);
});




//These two functions are for previewing the uploaded image.
$(document).on('change','input.preview',function(){
    readURL(this);
});
function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.portrait img').attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}


