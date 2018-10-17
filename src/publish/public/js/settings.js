function openSettings(blockId, modelId, submodelId){
    $("#settings").css("display", "block");

    $.ajax({
        url: "/admin/settings/" + blockId + '/' + modelId + '/' + (submodelId!==undefined?submodelId:''),
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get settings data.");
            console.log(data);
            $("#settings").html('<div class="error alert alert-danger"></div>');
            if(data.responseJSON !== undefined){
                $("#settings .error").append('<h2>'+data.responseJSON.exception+'</h2>');
                $("#settings .error").append('<h5>'+data.responseJSON.file+' line '+data.responseJSON.line+'</h5>');
                $("#settings .error").append('<h4>'+data.responseJSON.message+'</h4>');
                $("#settings .error").append('<ul></ul>');
                $.each(data.responseJSON.trace, function(i, item) {
                    $("#settings .error ul").append('<li>'+item.file+' line '+item.line+'; '+item.function+'()</li>');
                });
            }
            else{
                $("#settings .error").append(data.responseText);
            }
        }
    });
}

function openPanelSettings(pageId, panelLocation, blockId){
    $("#settings").css("display", "block");

    $.ajax({
        url: "/admin/settings/panel/" + pageId + '/' + panelLocation + '/' + (blockId!==undefined?blockId:''),
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get settings data.");
            console.log(data);
        }
    });
}

function openPageSettings(pageId){
    $("#settings").css("display", "block");

    $.ajax({
        url: "/admin/settings/page/" + pageId,
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get settings data.");
            console.log(data);
        }
    });
}

function openPageList(){
    $("#settings").css("display", "block");

    $.ajax({
        url: "/admin/settings/page/list",
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get settings data.");
            console.log(data);
        }
    });
}

function openLayoutSettings(layoutId){
    $("#settings").css("display", "block");

    $.ajax({
        url: "/admin/settings/layout/" + layoutId,
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get settings data.");
            console.log(data);
        }
    });
}

function openLayoutList(){
    $("#settings").css("display", "block");

    $.ajax({
        url: "/admin/settings/layout/list",
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get settings data.");
            console.log(data);
        }
    });
}

function openAddonList(){
    $("#settings").css("display", "block");

    $.ajax({
        url: "/admin/settings/addon/list",
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get settings data.");
            console.log(data);
        }
    });
}

function openAddonSettings(pageId){
    $("#settings").css("display", "block");

    $.ajax({
        url: "/admin/settings/addon/" + pageId,
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get settings data.");
            console.log(data);
        }
    });
}

function openCropping(blockId, modelId){
    $.ajax({
        url: "/admin/settings/crop/" + blockId + '/' + modelId,
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get cropping data.");
            console.log(data);
        }
    });
}

/**
 * Normalize content order
 * 
 * @param {int} blockId
 * @returns {void}
 */
function normalizeContent(blockId){
    $.ajax({
        url: "/admin/settings/normalize/" + blockId,
        error: function(data){
            console.log("Cannot normalize content.");
            console.log(data);
        }
    });
}

function openSetup(blockId){
    $.ajax({
        url: "/admin/settings/setup/" + blockId,
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get setup data.");
            console.log(data);
        }
    });
}

function closeSettings(){
    $("#settings").css("display", "none");
    location.reload();
}

function deleteModel(blockId, modelId, submodelId){
    var cnf = confirm('Do you want to delete this item?');

    if(cnf){
        $.ajax({
            url: "/admin/delete",
            method: "POST",
            data: {
                block_id: blockId,
                id: modelId,
                subid: submodelId,
                _token: $("meta[name=csrf-token]").attr('content')
            },
            dataType: "html",
            success: function(data){
                if(modelId != 0){
                    normalizeContent(blockId);
                    openSettings(blockId, submodelId!==undefined?modelId:0);
                }
                else{
                    openPanelSettings(JSON.parse(data).page_id, JSON.parse(data).panelLocation);
                }
            },
            error: function(data){
                console.log("Cannot delete block");
                console.log(data);
            }
        }); 
    }
}

function deletePage(pageId){
    var cnf = confirm('Do you want to delete this page? All data related to it will be lost!');

    if(cnf){
        $.ajax({
            url: "/admin/page/delete",
            method: "POST",
            data: {
                page_id: pageId,
                _token: $("meta[name=csrf-token]").attr('content')
            },
            dataType: "html",
            success: function(data){
                openPageList();
            },
            error: function(data){
                console.log("Cannot delete page");
                console.log(data);
            }
        }); 
    }
}

function deleteAddon(addonId){
    var cnf = confirm('Do you want to delete this addon? All data related to it will be lost!');

    if(cnf){
        $.ajax({
            url: "/admin/addon/delete",
            method: "POST",
            data: {
                addon_id: addonId,
                _token: $("meta[name=csrf-token]").attr('content')
            },
            dataType: "html",
            success: function(data){
                console.log(data);
                openAddonList();
            },
            error: function(data){
                console.log("Cannot delete addon");
                console.log(data);
            }
        });
    }
}

function deleteLayout(layoutId){
    var cnf = confirm('Do you want to delete this layout? All data related to it will be lost!');

    if(cnf){
        $.ajax({
            url: "/admin/layout/delete",
            method: "POST",
            data: {
                layout_id: layoutId,
                _token: $("meta[name=csrf-token]").attr('content')
            },
            dataType: "html",
            success: function(data){
                if(data.error.length){
                    $(".errors").removeClass('hide');
                    $(".errors").append('<ul></ul>');
                    $(".errors ul").append('<li>'+data.error+'</li>');
                }
                else{
                    openLayoutList();
                }
            },
            error: function(data){
                console.log("Cannot delete layout");
                console.log(data);
            }
        }); 
    }
}

function move(dir, blockId, modelId, submodelId){
    if(dir !== 'up'){
        dir = 'down';
    }
    $.ajax({
            url: "/admin/move"+dir,
            method: "POST",
            data: {
                block_id: blockId,
                id: modelId,
                subid: submodelId,
                _token: $("meta[name=csrf-token]").attr('content')
            },
            dataType: 'html',
            success: function(data){
                if(modelId === 0){
                    openPanelSettings(JSON.parse(data).page_id, JSON.parse(data).panelLocation);
                }
                else{
                    openSettings(blockId, submodelId!==undefined?modelId:0);
                }
            },
            error: function(data){
                console.log("Cannot move block "+dir);
                console.log(data);
            }
        }); 
}

function moveTo(newPosition, blockId, modelId, submodelId){
    $.ajax({
        url: "/admin/moveto",
        method: "POST",
        data: {
            newPosition: newPosition,
            block_id: blockId,
            id: modelId,
            subid: submodelId,
            _token: $("meta[name=csrf-token]").attr('content')
        },
        dataType: 'html',
        success: function(data){
            if(modelId === 0){
                openPanelSettings(JSON.parse(data).page_id, JSON.parse(data).panelLocation);
            }
            else{
                var currentPosition = parseInt($('.gallery-settings-grid-item[data-id='+modelId+']').attr('data-no'));
                newPosition = parseInt(newPosition);
                
                if(currentPosition < newPosition){
                    $('.gallery-settings-grid-item').each(function(){
                        if( parseInt($(this).attr('data-no')) > currentPosition && parseInt($(this).attr('data-no')) <= newPosition ){
                            $(this).attr('data-no', (parseInt($(this).attr('data-no')) - 1) );
                        }
                    });
                }
                else{
                    $('.gallery-settings-grid-item').each(function(){
                        if( parseInt($(this).attr('data-no')) < currentPosition && parseInt($(this).attr('data-no')) >= newPosition ){
                            $(this).attr('data-no', (parseInt($(this).attr('data-no')) + 1) );
                        }
                    });
                }
                
                $('.gallery-settings-grid-item[data-id='+modelId+']').attr('data-no', newPosition);
            }
        },
        error: function(data){
            console.log("Cannot move block "+dir);
            console.log(data);
        }
    });
}

function activate(newState, blockId, modelId, submodelId){
    if(newState !== 1){
        newState = 0;
    }
    $.ajax({
        url: "/admin/"+(newState?"":"de")+"activate",
        method: "POST",
        data: {
            block_id: blockId,
            id: modelId,
            subid: submodelId,
            _token: $("meta[name=csrf-token]").attr('content')
        },
        dataType: 'html',
        success: function(data){
            if(modelId === 0){
                openPanelSettings(JSON.parse(data).page_id, JSON.parse(data).panelLocation);
            }
            else{
                openSettings(blockId, submodelId!==undefined?modelId:0);
            }
        },
        error: function(data){
            console.log("Cannot change block visability");
            console.log(data);
        }
    }); 
}

function dragItems(container, blockId){
    var drake = dragula([document.getElementById(container)]);

    drake.on('drop', function(el, target, source, sibling){
        var dropPosition = $(sibling).attr("data-no");
        if ($(sibling).attr("data-no") === undefined){
            dropPosition = $(el).siblings().length + 2;
        }
        var newPosition = dropPosition < $(el).attr("data-no") ? dropPosition : (dropPosition - 1);
        moveTo(newPosition, blockId, parseInt($(el).attr("data-id")));
    });
}

function openChangePassword(){
    $("#settings").css("display", "block");

    $.ajax({
        url: "/admin/settings/password",
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get settings data.");
            console.log(data);
        }
    });
}