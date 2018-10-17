<div class='col-md-12'>
    <div class='col-md-11'>
        <h4>
            Settings :: Page
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-close"></i></a>
    </div>
</div>

<?php
$form = $page->settingsForm();
?>
<div class='col-md-12' id="page_{{ $page->id }}_settingsForm">
    @include('Just.form')
</div>

<script>
    CKEDITOR.replace('description');
    var currentData = formData($("#page_{{ $page->id }}_settingsForm form"));
    
    $("#page_{{ $page->id }}_settingsForm form").ajaxForm({
        beforeSerialize: function(form, options) {
            for (instance in CKEDITOR.instances){
                CKEDITOR.instances[instance].updateElement();
            }
        },
        success: function(data){
            closeSettings();
        },
        error: function(data){
            console.log(data);
            $(".errors").removeClass('hide');
            $(".errors").append('<ul></ul>');
            $.each(data.responseJSON.errors, function(i, item) {
                $(".errors ul").append('<li>'+item+'</li>');
            });
            $(".errors ul").append('<li>'+data.responseJSON.message+'</li>');
            
            $.ajax({
                url: '/admin/settings/page/setup',
                method: "POST",
                data: currentData
            });
        }
    });
    
    function formData($form){
        var unindexed_array = $form.serializeArray();
        var indexed_array = {};

        $.map(unindexed_array, function(n, i){
            indexed_array[n['name']] = n['value'];
        });

        return indexed_array;
    }
</script>
