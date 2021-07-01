<template>

    <div class="createGalleryItem">

        <div v-show="stage === 'choose'" class="settings-list-component">
            <div class="thumbnail" v-for="(item, key) in listItem" :key="key">
                <div class="caption">
                    <a href="#" @click.prevent="choose(key)">
                        <h1 class='featureItem'>
                            <i :class="'fa'+(!!item.brandIcon?'b':'')+' fa-' + (!!item.brandIcon ? item.brandIcon : item.icon)"></i>
                        </h1>

                        <strong>{{ item.label }}</strong>
                    </a>
                </div>
            </div>
        </div>

        <ajax-uploader v-show="stage === 'upload'" :input-name="uploader.inputName"
                       :multiple="uploader.multiple"
                       :allowed-extensions="uploader.allowedExtensions"
                       :upload-url="uploader.uploadUrl"
                       :additional-parameters="uploader.additionalParameters"
        ></ajax-uploader>

        <input-text v-show="stage === 'url'" name="youTubeUrl"
                    :value="content"
                    label="YouTube URL"
                    :parameters="{}"
                    @input="handleInput"
        ></input-text>

    </div>

</template>

<script>
    import AjaxUploader from "./AjaxUploader";
    import { InputText } from "lubart-vue-input-component";

    export default {
        name: "CreateGalleryItem",

        components: { AjaxUploader, InputText },

        props: {
            token: {type: String},
            additionalParameters: {type: Object, default: ()=>({})},
            value: {type: String, default: ""}      // value for the youTubeUrl if it exists
        },

        data(){
            return {
                stage: 'choose',  // possible values are: choose, upload or url
                listItem: {
                    singleItem: {
                        label: "Single Item",
                        icon: "image"
                    },
                    multipleItems: {
                        label: "Upload Multiple Images",
                        icon: "images"
                    }
                },
                uploader: {
                    inputName: 'image',
                    multiple: false,
                    allowedExtensions: ['png', 'jpg', 'jpeg'],
                    uploadUrl: "/settings/block/item/save",
                    additionalParameters: this.additionalParameters
                },
                content: this.value
            }
        },

        methods:{
            choose(key){
                if(this.stage === 'choose'){
                    switch (key){
                        case 'singleItem':
                            this.listItem = {
                                photo: {
                                    label: "Photo",
                                    icon: "image"
                                },
                                video: {
                                    label: "Video",
                                    icon: "film"
                                }
                            }
                            break;
                        case 'multipleItems':
                            this.stage = 'upload';
                            this.uploader.multiple = true;
                            this.uploader.allowedExtensions = ['png', 'jpg', 'jpeg'];
                            this.uploader.inputName = 'image';
                            break;
                        case 'photo':
                            this.stage = 'upload';
                            this.uploader.multiple = false;
                            this.uploader.allowedExtensions = ['png', 'jpg', 'jpeg'];
                            this.uploader.inputName = 'image';
                            break;
                        case 'video':
                            this.listItem = {
                                uploadVideo: {
                                    label: "Upload Video",
                                    icon: "film"
                                },
                                url: {
                                    label: "YouTube URL",
                                    brandIcon: "youtube"
                                }
                            }
                            break;
                        case 'uploadVideo':
                            this.stage = 'upload';
                            this.uploader.multiple = false;
                            this.uploader.allowedExtensions = ['mp4'];
                            this.uploader.inputName = 'video';
                            break;
                        case 'url':
                            this.stage = 'url';
                            break;
                    }
                }
            },

            handleInput(val){
                this.content = val;
                this.$parent.content = this.content;
            }
        }
    }
</script>

<style lang="scss">

    .settings-list-component > div{
        flex: 1 0 48%;
        margin: 5px;
    }

    a.featureIcon{
        padding: 10px;
        font-size: medium;
    }

    div.iconSet div.chosenIcon{
        font-size: 50pt;
        padding: 20px;
        flex-basis: 15%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .iconSet{
        display: flex;
        flex-wrap: wrap;
    }

    div.bundleSelection{
        flex-basis: 100%;
    }

    div.iconBundle{
        flex-basis: 85%;
        display: flex;
        flex-wrap: wrap;
    }

    .iconBundle a{
        flex-basis: 40px;
    }

</style>
