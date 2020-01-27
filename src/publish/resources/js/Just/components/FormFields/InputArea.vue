<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <fieldset v-if="parameters.translate === true">
            <div v-for="(label, lang) in languages" class="input-text-component__translate-container">
                <div>{{ label }}</div>
                <div>
                    <vue-editor v-if="richEditor" :customModules="customModulesForEditor" :editorOptions="editorSettings" :id="lang" :lang="lang" v-model="content[lang]" :editorToolbar="editorToolbar"></vue-editor>
                    <textarea v-else :name="name + '-' + lang" :id="name + '-' + lang" cols="30" rows="10" @input="handleInput" v-bind="parameters">{{ content[lang] }}</textarea>
                </div>
            </div>
        </fieldset>
        <span v-else>
            <vue-editor v-if="richEditor" :customModules="customModulesForEditor" :editorOptions="editorSettings" v-model="content" :editorToolbar="editorToolbar"></vue-editor>
            <textarea v-else :name="name" :id="name" cols="30" rows="10" @input="handleInput" v-bind="parameters">{{ content }}</textarea>
        </span>
    </block>

</template>

<script>
    import { VueEditor } from "vue2-editor";
    import ImageResize from "quill-image-resize-module";
    import VideoResize from 'quill-video-resize-module';
    //import ImageUploader from "quill.imageUploader.js"; // npm install quill-image-uploader --save
    import { htmlEditButton } from "quill-html-edit-button";
    import InputBase from './InputBase';

    export default {

        extends: InputBase,

        components: {
            VueEditor
        },

        props: {
            parameters: { type: Object },
            value: {}
        },

        data() {
            return {
                content: this.value === null ? (this.parameters.translate ? {} : '') : this.value,
                editorToolbar: [
                    ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                    [{ 'align': ''}, { 'align': 'center'}, {'align': 'right'}, {'align': 'justify'}],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                    [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
//                    [{ 'direction': 'rtl' }],                         // text direction
                    [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                    [{ 'size': [] }],  // custom dropdown
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

                    [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
//                    [{ 'font': [] }],                                 // normally website should use theme font family

                    [ 'link', 'image', 'video' ],

                    ['clean']
                ],
                customModulesForEditor: [{ alias: "imageResize", module: ImageResize }, { alias: "videoResize", module: VideoResize }, { alias: "htmlEditButton", module: htmlEditButton }],
                editorSettings: {
                    modules: {
                        imageResize: {},
                        videoResize: {},
                        htmlEditButton: {}
                    }
                },
                languages:{
                    en: 'English',
                    uk: 'Українська'
                },
                richEditor: this.parameters.richEditor == undefined ? true : !!this.parameters.richEditor
            };
        },

        created(){
            if(this.parameters.translate === true && _.isEmpty(this.value)){
                this.content = {};
                Object.keys(this.languages).forEach(lang=>{
                    this.content[lang] = "";
                });
            }
        },

        watch: {
            content(val){
                this.$emit('input', val);
            }
        }
    };
</script>

<style>
    .ql-container iframe {
        pointer-events: none;
    }

    .ql-toolbar.ql-snow{
        border: 1px solid #dbdbdb;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }

    .ql-container.ql-snow{
        border: 1px solid #dbdbdb;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
    }
</style>