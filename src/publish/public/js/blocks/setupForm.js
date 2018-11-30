$(document).ready(function(){
    var $cropDimentions = $("label[for=cropDimentions]").parent();
    
    $cropDimentions.toggle();
    
    if($('input[name=cropPhoto]').is(':checked')){
        $cropDimentions.toggle();
    }
    
    $('input[name=cropPhoto]').change(function(){
        $cropDimentions.toggle();
    });
    
    var $photoSizes = $('input[name^=photoSizes]').parent().parent();
    
    $photoSizes.toggle();
    
    if($('input[name=customSizes]').is(':checked')){
        $photoSizes.toggle();
    }
    
    $('input[name=customSizes]').change(function(){
        $photoSizes.toggle();
    });
});