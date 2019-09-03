<div class='col-md-12'>
    <div class='col-md-11'>
        <h4>
            @lang('settings.title') :: @lang('user.title') :: @lang('user.createForm.title')
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='@lang('settings.actions.close')'><i class="fa fa-times"></i></a>
    </div>
</div>

<div id="userSettingsForm"  class='col-md-12'>
    {!! $user->settingsForm()->render() !!}
</div>

<script>
    $("#userSettingsForm form").ajaxForm({
        beforeSerialize: function(form, options) {
            $("input[type=submit]").attr('disabled', 'disabled');
            for (instance in CKEDITOR.instances){
                CKEDITOR.instances[instance].updateElement();
            }
        },
        success: function(data){
            openList('user');
        },
        error: function(data){
            console.log(data);
            $("input[type=submit]").removeAttr('disabled');
            showErrors(data);
        }
    });
</script>