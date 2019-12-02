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
