<div class='col-md-12'>
    <div class='col-md-11'>
        <h4>
            Settings :: User
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-close"></i></a>
    </div>
</div>

<div id="userSettingsForm"  class='col-md-12'>
    {!! $user->settingsForm()->render() !!}
</div>

<script>
    $("#userSettingsForm form").ajaxForm({
        beforeSerialize: function(form, options) {
            for (instance in CKEDITOR.instances){
                CKEDITOR.instances[instance].updateElement();
            }
        },
        success: function(data){
            openList('user');
        },
        error: function(data){
            console.log(data);
            showErrors(data);
        }
    });
</script>