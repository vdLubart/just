var panelNo = 1;
var panelElement;
var panelTypeElement;

$(document).ready(function(){
    panelElement = $("#panel").parent('div');
    panelTypeElement = $("#panelType").parent('div');
    
    $("#panel").parent('div').remove();
    $("#panelType").parent('div').remove();
    
    addPanel();
});

function addPanel(){
    if($("input[name=layout_id]").val() > 0){
        $("#addPanel").addClass("hide");
        return;
    }
    
    var elPanel = panelElement.clone();
    var elType= panelTypeElement.clone();
    
    
    $("#layoutPanels").append('<div class="col-md-3" id="layoutPanel_'+panelNo+'"></div>');
    $("#layoutPanel_"+panelNo).append(elPanel);
    $("#layoutPanel_"+panelNo).children('div').children('label[for=panel]').attr('for', 'panel_'+panelNo);
    $("#layoutPanel_"+panelNo).children('div').children('select#panel').attr('id', 'panel_'+panelNo).attr('name', 'panel_'+panelNo);
    $("#layoutPanel_"+panelNo).append(elType);
    $("#layoutPanel_"+panelNo).children('div').children('label[for=panelType]').attr('for', 'panelType_'+panelNo);
    $("#layoutPanel_"+panelNo).children('div').children('select#panelType').attr('id', 'panelType_'+panelNo).attr('name', 'panelType_'+panelNo);
    
    $("#layoutPanel_"+panelNo).append("<a href='javascript:removePanel("+panelNo+")'> " + $("#element-removePanelSpan span").html() + " </a>");
    
    panelNo++;
}

function removePanel(no){
    $("#layoutPanel_"+no).remove();
}