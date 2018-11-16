$(document).ready(function(){
    
    getIconList();
    
    $('#iconSet').change(function(){
        getIconList();
    });
    
    applyCKEditor('#features_settingsForm #description');
});

function getIconList(page){
    
    if(page === undefined){
        page = 1;
    }
    
    $.ajax({
        url: "/iconset/" + $("#iconSet").val() + '/' + page,
        method: 'POST',
        data: {
            _token: $("meta[name=csrf-token]").attr('content')
        },
        success: function (data) {
            $("#icons").html('');
            $.each(data.data, function(index, item){
                console.l
                $("#icons").append("<a href='javascript:chooseIcon("+item.id+")' data-id='"+item.id+"' class='featureIcon'><"+ item.icon_set.tag +" class='"+ item.icon_set.class +" "+item.class+"'></"+ item.icon_set.tag +"></a>");
            });
            $("#icons").append('<div id="iconsPaginator"><a href="javascript:getIconList('+(data.current_page-1 > 0 ? (data.current_page-1): 1)+')"><i class="fa fa-angle-left"></i> Previous</a> <a href="javascript:getIconList('+(data.current_page+1 < data.last_page ? (data.current_page+1) : data.current_page)+')">Next <i class="fa fa-angle-right"></i></a></div>');
            
        },
        error: function (data) {
            console.log("Cannot get icons data.")
            console.log(data);
        }
    });
}

function chooseIcon(id){
    var icon = $("a[data-id="+id+"]");
    $("input[name=icon]").val(id);
    
    $("#currentIcon").html($(icon).html());
}