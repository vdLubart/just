<div class='col-md-12'>
    <div class='col-md-11'>
        <h4>
            @lang('settings.title') :: @lang('layout.title') :: @if($layout->id == 0) @lang('layout.createForm.title') @else @lang('layout.editForm.title') @endif
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='@lang('settings.actions.close')'><i class="fa fa-times"></i></a>
    </div>
</div>

<div id="layout_{{ $layout->id }}_settingsForm" class="col-md-12">
    {!! $layout->settingsForm()->render() !!}
</div>

<script>
    $("#layout_{{ $layout->id }}_settingsForm form").ajaxForm({
        beforeSerialize: function(form, options) {
            $("input[type=submit]").attr('disabled', 'disabled');
        },
        success: function(data){
            closeSettings();
        },
        error: function(data){
            console.log(data);
            $("input[type=submit]").removeAttr('disabled');
            showErrors(data);
        }
    });
</script>
