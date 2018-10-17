<div class='col-md-12'>
    <div class='col-md-11'>
        <h4>
            Settings :: Layout
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-close"></i></a>
    </div>
</div>

<?php
$form = $layout->settingsForm();
?>
<div id="layout_{{ $layout->id }}_settingsForm" class="col-md-12">
    @include('Just.form')
</div>

<script>
    $("#layout_{{ $layout->id }}_settingsForm form").ajaxForm({
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
        }
    });
</script>
