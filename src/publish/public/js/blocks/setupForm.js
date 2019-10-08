$(document).ready(function(){
    var $cropDimensions = $("label[for=cropDimensions]").parent();

    $cropDimensions.toggle();
    
    if($('input[name=cropPhoto]').is(':checked')){
        $cropDimensions.toggle();
    }
    
    $('input[name=cropPhoto]').change(function(){
        $cropDimensions.toggle();
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