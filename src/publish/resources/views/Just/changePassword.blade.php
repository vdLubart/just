<div class='col-md-12'>
    <div class='col-md-10'>
        <h4>
            Settings :: User :: Change Password
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-close"></i></a>
    </div>
</div>
<div id="changePassordForm" class='col-md-12'>
    {!! $form->render() !!}
</div>


<script>
    $("#changePassordForm form").ajaxForm({
        beforeSerialize: function(form, options) {
            for (instance in CKEDITOR.instances){
                CKEDITOR.instances[instance].updateElement();
            }
        },
        success: function(data){
            console.log(data);
            $("#changePassordForm").prepend("<div class='alert alert-success'><ul><li>Password is updated successfully</li></ul></div>");
        },
        error: function(data){
            console.log(data);
            showErrors(data);
        }
    });
</script>