<template>

    <div>
        <span v-if="isWaitingData">Loading data...</span>
        <div v-else>
            <cropper
                    classname="cropper"
                    :src="imageSource"
                    :stencilProps="{aspectRatio: ratio}"
                    @change="change"
            ></cropper>

            <footer class="settings-component__card__footer">
                <input-button :disabled="false" :label="__('actions.crop')" @click="submitForm"></input-button>
            </footer>
        </div>

    </div>

</template>

<script>
    import { Cropper } from 'vue-advanced-cropper';
    import 'cropperjs/dist/cropper.css';
    import Field from './FormField';
    import {InputButton} from 'lubart-vue-input-component';

    export default {
        name: "Crop",

        components: { Cropper, Field, InputButton},

        props: ['content', 'isWaitingData'],

        data(){
            return {
                parameters: this.$parent.$parent.responseParameters,
                imageSource: '',
                ratio: null,
                formData: {
                    id: 0,
                    block_id: 0,
                    x: 0,
                    y: 0,
                    w: 500,
                    h: 500,
                    img: ''
                }
            }
        },

        created(){
            this.imageSource = this.parameters.image;

            let ratio = _.split(this.$parent.$parent.responseParameters.dimensions, ':');
            if(ratio.length == 2) {
                this.ratio = ratio[0] / ratio[1];
            }

            this.formData.id = this.parameters.itemId;
            this.formData.block_id = this.parameters.blockId;
            this.formData.h = this.formData.w / this.ratio;
            this.formData.img = this.parameters.imageCode;
        },

        methods:{
            change(crop){
                console.log(crop);
                this.formData.x = crop.coordinates.left;
                this.formData.y = crop.coordinates.top;
                this.formData.w = crop.coordinates.width;
                this.formData.h = crop.coordinates.height;
            },

            submitForm(){
                axios({
                    method: 'post',
                    url: '/settings/block/item/crop',
                    data: this.formData
                })
                    .then((response) => {
                        if(response.data.redirect !== null) {
                            this.$root.navigate(response.data.redirect).then(()=>{
                                this.$parent.$parent.showSuccessMessage(response.data.message);

                                setTimeout(this.$parent.$parent.resetAlert, 5000);
                            });
                        }

                        this.$parent.$parent.showSuccessMessage(response.data.message);
                    })
                    .catch((response) => {
                        console.error("Cannot send data.");
                        console.error(response.response);
                        this.$parent.$parent.showErrors(response.response.data);
                    });
            }
        }
    }
</script>

<style scoped>

    .settings-component__card__footer{
        background-color: #f5f5f5;
        display: flex;
        flex-shrink: 0;
        justify-content: flex-end;
        padding: 20px;
        position: relative;
        margin: 10px -10px -20px -10px;
    }

</style>