$(document).ready(function(){
    var $cropDimentions = $("label[for=cropDimentions]").parent();
    
    $cropDimentions.toggle();
    
    if($('input[name=cropPhoto]').is(':checked')){
        $cropDimentions.toggle();
    }
    
    $('input[name=cropPhoto]').change(function(){
        $cropDimentions.toggle();
    });
});