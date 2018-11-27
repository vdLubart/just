$(document).ready(function(){
    $.getScript("/js/ajaxupload.js", function(){
        $('#imageUploader').ajaxupload({
            url: "/admin/ajaxuploader",
            remotePath: "../storage/app/public/logos",
            maxFileSize: '8M',
            maxFiles: 1,
            allowExt: ['jpg', 'png', 'jpeg'],
            autoStart: false,
            overrideFile: true,
            formParams: getFormParams(),
            onInit: function ()
            {
                //hide remove files and upload button
                this.removeFiles.hide();
                this.uploadFiles.hide();
            },
            onSelect: function (files) {
                $("input[name=startUpload]")[0].outerHTML = '<button name="startUpload" type="button">Update image</button>';
                clickButton();
            },
            finish: function (files, filesObj) {
                alert('All files have been uploaded!');
            },
            success: function (file) {
                console.log('File ' + file + ' uploaded correctly');
            },
            error: function (txt, obj, json, url) {
                console.log(json);
                alert('An error occour: ' + txt);
            }
        });
    });
    
    clickButton();
    
    applyCKEditor('#logo_settingsForm #description');
    
    /**
     * Return all form data included _token
     * 
     * @returns JSON object
     */
    function getFormParams(){
        var params = {};
        
        var form = $('#imageUploader').parent().parent('form');
        
        $.each($(form).serializeArray(), function(index, item){
            params[item.name] = item.value;
        })
        
        return params;
    }
    
    function clickButton(){
        $("button[name=startUpload]").click(function(){
            if($("input[name=id]").val() == 0 || $('#imageUploader').data('AU').files.length > 0){
                $('#imageUploader').data('AU').settings.formParams = getFormParams();
                $('#imageUploader').ajaxupload('start');
                $(this).css('display', 'none');
            }
        });
    }
});