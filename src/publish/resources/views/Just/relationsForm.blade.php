{!! $block->relationsForm(@$relBlock)->render() !!}

<script>
    $("#{{ $block->name }}_relationsForm form").ajaxForm({
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
                openSettings({{ $block->id }}, 0);
            }
        },
        error: function(data){
            console.log(data);
            showErrors(data);
        }
    });
</script>