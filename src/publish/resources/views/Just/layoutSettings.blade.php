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

<div id="layout_{{ $layout->id }}_settingsForm" class="col-md-12">
    {!! $layout->settingsForm()->render() !!}
</div>

<script>
    $("#layout_{{ $layout->id }}_settingsForm form").ajaxForm({
        success: function(data){
            closeSettings();
        },
        error: function(data){
            console.log(data);
            showErrors(data);
        }
    });
</script>
