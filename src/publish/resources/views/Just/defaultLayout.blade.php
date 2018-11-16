<div class='col-md-12'>
    <div class='col-md-10'>
        <h4>
            Settings :: Layout :: Set Default
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-close"></i></a>
    </div>
</div>
<div id="setDefaultLayoutForm" class='col-md-12'>
    {!! $form->render() !!}
</div>


<script>
    $("#setDefaultLayoutForm form").ajaxForm({
        beforeSerialize: function(form, options) {
            for (instance in CKEDITOR.instances){
                CKEDITOR.instances[instance].updateElement();
            }
        },
        success: function(data){
            console.log(data);
            location.reload();
        },
        error: function(data){
            console.log(data);
            showErrors(data);
        }
    });
</script>