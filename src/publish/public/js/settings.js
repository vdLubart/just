/*
 * Open settings for blocks, pages, layouts and addons
 * 
 * @param number|string blockId block id for numbers and object name for strings
 * @param number modelId id for block, page, layout or addon
 * @returns {undefined}
 */
function openSettings(blockId, modelId){
    $("#settings").css("display", "block");

    $.ajax({
        url: "/admin/settings/" + blockId + '/' + modelId,
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get settings data.");
            console.log(data);
            showErrors(data);
        }
    });
}

function showErrors(data){
    if(data.responseJSON !== undefined){
        if(data.responseJSON.errors !== undefined){
            $("#settings form").prepend('<div class="error alert alert-danger"></div>');
            $(".error").append('<ul></ul>');
            $.each(data.responseJSON.errors, function(i, item) {
                $(".error ul").append('<li>'+item+'</li>');
            });
        }
        else{
            $("#settings").html('<div class="error alert alert-danger"></div>');
            $("#settings .error").append('<h2>'+data.responseJSON.exception+'</h2>');
            $("#settings .error").append('<h5>'+data.responseJSON.file+' line '+data.responseJSON.line+'</h5>');
            $("#settings .error").append('<h4>'+data.responseJSON.message+'</h4>');
            $("#settings .error").append('<ul></ul>');
            $.each(data.responseJSON.trace, function(i, item) {
                $("#settings .error ul").append('<li>'+item.file+' line '+item.line+'; '+item.function+'()</li>');
            });
        }
    }
    else if(data.responseText !== undefined){
        $("#settings").html('<div class="error alert alert-danger"></div>');
        $("#settings .error").append(data.responseText);
    }
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
            showErrors(data);
        }
    });
}

function openList(item){
    $("#settings").css("display", "block");

    $.ajax({
        url: "/admin/settings/"+item+"/list",
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get settings data.");
            console.log(data);
            showErrors(data);
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
            showErrors(data);
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
            showErrors(data);
        }
    });
}

function openSetup(blockId){
    return openSettings('setup', blockId);
}

function closeSettings(){
    $("#settings").css("display", "none");
    location.reload();
}

function deleteModel(blockId, modelId){
    var cnf = confirm('Do you want to delete this item?');

    if(cnf){
        $.ajax({
            url: "/admin/delete",
            method: "POST",
            data: {
                block_id: blockId,
                id: modelId,
                _token: $("meta[name=csrf-token]").attr('content')
            },
            dataType: "html",
            success: function(data){
                if(modelId != 0){
                    normalizeContent(blockId);
                    openSettings(blockId, modelId);
                }
                else{
                    openPanelSettings(JSON.parse(data).page_id, JSON.parse(data).panelLocation);
                }
            },
            error: function(data){
                console.log("Cannot delete block");
                console.log(data);
                showErrors(data);
            }
        }); 
    }
}

/**
 * Delete item. Works for page, addon, category
 * 
 * @param string item page, addon or category
 * @param number id
 * @returns {undefined}
 */
function deleteItem(item, id){
    var cnf = confirm('Do you want to delete this '+item+'? All data related to it will be lost!');

    if(cnf){
        $.ajax({
            url: "/admin/"+item+"/delete",
            method: "POST",
            data: {
                id: id,
                _token: $("meta[name=csrf-token]").attr('content')
            },
            dataType: "html",
            success: function(data){
                openList(item);
            },
            error: function(data){
                console.log("Cannot delete "+item);
                console.log(data);
                showErrors(data);
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
                if(data.error !== undefined){
                    $(".errors").removeClass('hide');
                    $(".errors").append('<ul></ul>');
                    $(".errors ul").append('<li>'+data.error+'</li>');
                }
                else{
                    openList('layout');
                }
            },
            error: function(data){
                console.log("Cannot delete layout");
                console.log(data);
                showErrors(data);
            }
        }); 
    }
}

function move(dir, blockId, modelId){
    if(dir !== 'up'){
        dir = 'down';
    }
    $.ajax({
            url: "/admin/move"+dir,
            method: "POST",
            data: {
                block_id: blockId,
                id: modelId,
                _token: $("meta[name=csrf-token]").attr('content')
            },
            dataType: 'html',
            success: function(data){
                if(modelId === 0){
                    openPanelSettings(JSON.parse(data).page_id, JSON.parse(data).panelLocation);
                }
                else{
                    openSettings(blockId, 0);
                }
            },
            error: function(data){
                console.log("Cannot move block "+dir);
                console.log(data);
                showErrors(data);
            }
        }); 
}

function moveTo(newPosition, blockId, modelId){
    $.ajax({
        url: "/admin/moveto",
        method: "POST",
        data: {
            newPosition: newPosition,
            block_id: blockId,
            id: modelId,
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
            showErrors(data);
        }
    });
}

function activate(newState, blockId, modelId){
    if(newState !== 1){
        newState = 0;
    }
    $.ajax({
        url: "/admin/"+(newState?"":"de")+"activate",
        method: "POST",
        data: {
            block_id: blockId,
            id: modelId,
            _token: $("meta[name=csrf-token]").attr('content')
        },
        dataType: 'html',
        success: function(data){
            if(modelId === 0){
                openPanelSettings(JSON.parse(data).page_id, JSON.parse(data).panelLocation);
            }
            else{
                openSettings(blockId, 0);
            }
        },
        error: function(data){
            console.log("Cannot change block visability");
            console.log(data);
            showErrors(data);
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
            showErrors(data);
        }
    });
}

/*
 * Open settings for blocks, pages, layouts and addons
 * 
 * @param number|string blockId block id for numbers and object name for strings
 * @param number modelId id for block, page, layout or addon
 * @returns {undefined}
 */
function setDefaultLayout(){
    $("#settings").css("display", "block");

    $.ajax({
        url: "/admin/settings/layout/default",
        success: function(data){
            $("#settings").html(data);
        },
        error: function(data){
            console.log("Cannot get settings data.");
            console.log(data);
            showErrors(data);
        }
    });
}

/**
 * Replace defined by CSS query textarea by CKEDITOR instance
 * 
 * @param string cssQuery query to defind element
 * @returns void
 */
function applyCKEditor(cssQuery){
    var featureDescription = document.querySelector(cssQuery);
    CKEDITOR.replace(featureDescription);
}