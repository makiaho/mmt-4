$(document).ready(function(){
    
    $('.message').delay(2500).fadeOut(1000);
    
});


$(document).on('click','a.toggle',function(){
    
    $('nav').toggle();
    
    return false;
    
});


$(document).on('click','a.chart-limit-toggle',function(){
    
    $('.chart-limits').toggle();
    
    return false;
    
});


$(document).on('click','a.chart-nav',function(){
    
    var itemCount = $('.chart-gallery .chart').length;
    
    var currentItem = 1;
    
    var newItem = 1;
    
    $('.chart-gallery .chart').each(function(i){
        
        if($(this).hasClass('selected')){
            currentItem = i + 1;
        }
        
    });
    
    if($(this).hasClass('prev')){
        newItem = currentItem - 1;
    }else if ($(this).hasClass('next')){
        newItem = currentItem + 1;
    }
    
    if(newItem > itemCount){
        newItem -= itemCount;
    }else if (newItem < 1){
        newItem += itemCount;
    }
    
    $('.chart-gallery .chart').removeClass('selected');
    
    $('.chart-gallery .chart').each(function(i){
        
        if(i + 1 === newItem){
            $(this).addClass('selected');
            
            var id = $(this).find('> div').attr('id');
            
            var chart = $('#' + id).highcharts();
            
            chart.setSize(null,null,false);
        }
        
    });
    
    
    
    return false;
    
});




$(document).mouseup(function (e) {

    if ($('nav').length > 0) {
        var container = $('header');

        if ((!container.is(e.target) && container.has(e.target).length === 0)) {

            $('nav').hide();
        }
    }
});


