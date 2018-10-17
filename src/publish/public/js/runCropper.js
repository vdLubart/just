
function runCroper(w, h){
    var $cropper = $(".cropper");
    
    $cropper.cropper({
        aspectRatio: w/h,
        data: {
            x: 0,
            y: 0,
            width: w,
            height: h
        },
        dashed: false,
        built: function(e) {
            console.log(e.type);
            setData($cropper);
        },
        dragend: function(e) {
            console.log(e.type);
            setData($cropper);
        }
    });
}

function setData($cropper){
    $("div[id$=_cropForm] input[name=x]").val($cropper.cropper("getData").x);
    $("div[id$=_cropForm] input[name=y]").val($cropper.cropper("getData").y);
    $("div[id$=_cropForm] input[name=w]").val($cropper.cropper("getData").width);
    $("div[id$=_cropForm] input[name=h]").val($cropper.cropper("getData").height);
}


