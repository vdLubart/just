<?php
$form = $block->blockForm();
?>

@include('Just.form')

<script>
    $("#{{ $block->name }}_blockData form").ajaxForm({
        beforeSerialize: function(form, options) {
            for (instance in CKEDITOR.instances){
                CKEDITOR.instances[instance].updateElement();
            }
        },
        success: function(data){
            console.log(data);
            if(data.shouldBeCropped){
                $.ajax({
                    url: "/admin/settings/crop/" + data.block_id + '/' + data.id,
                    success: function(data){
                        $("#settings").html(data);
                    },
                    error: function(data){
                        console.log("Cannot get settings data.")
                        console.log(data);
                    }
                });
            }
            else{
                openSettings({{ $block->id }}, {{ !is_null($block->submodel())? $block->model()->id : 0 }});
            }
        },
        error: function(data){
            console.log(data);
            $(".errors").removeClass('hide');
            $(".errors").append('<ul></ul>');
            $.each(data.responseJSON.errors, function(i, item) {
                $(".errors ul").append('<li>'+item+'</li>');
            });
        }
    });
    
    $(document).ready(function(){
        CKEDITOR.replace('description');
    });
</script>