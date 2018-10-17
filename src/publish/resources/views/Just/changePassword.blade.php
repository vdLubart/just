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
    @include('Just.form')
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
            $(".errors").removeClass('hide');
            $(".errors ul").remove();
            $(".errors").append('<ul></ul>');
            $.each(data.responseJSON.errors, function(i, item) {
                $(".errors ul").append('<li>'+item+'</li>');
            });
        }
    });
</script>