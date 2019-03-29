<div class='col-md-12'>
    <div class='col-md-11'>
        <h4>
            Settings :: Addon
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-times"></i></a>
    </div>
</div>

<div id="addonSettingsForm"  class='col-md-12'>
    {!! $addon->settingsForm()->render() !!}
</div>

<script>
    $("#addonSettingsForm form").ajaxForm({
        beforeSerialize: function(form, options) {
            $("input[type=submit]").attr('disabled', 'disabled');
            for (instance in CKEDITOR.instances){
                CKEDITOR.instances[instance].updateElement();
            }
        },
        success: function(data){
            openList('addon');
        },
        error: function(data){
            console.log(data);
            $("input[type=submit]").removeAttr('disabled');
            showErrors(data);
        }
    });
</script>