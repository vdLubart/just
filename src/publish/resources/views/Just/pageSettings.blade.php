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

<div class='col-md-12' id="page_{{ $page->id }}_settingsForm">
    {!! $page->settingsForm()->render() !!}
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
