<div class='col-md-12'>
    <div class='col-md-11'>
        <h4>
            Settings :: Page
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-times"></i></a>
    </div>
</div>

<div class='col-md-12' id="page_{{ $page->id }}_settingsForm">
    {!! $page->settingsForm()->render() !!}
</div>

<script>
    var currentData = formData($("#page_{{ $page->id }}_settingsForm form"));
    
    $("#page_{{ $page->id }}_settingsForm form").ajaxForm({
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
    
    function formData($form){
        var unindexed_array = $form.serializeArray();
        var indexed_array = {};

        $.map(unindexed_array, function(n, i){
            indexed_array[n['name']] = n['value'];
        });

        return indexed_array;
    }
</script>
