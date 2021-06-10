<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <fieldset v-if="parameters.translate === true">
            <div v-for="(label, lang) in languages" class="input-text-component__translate-container">
                <div>{{ label }}</div>
                <div>
                    <vue-editor v-if="richEditor" :useCustomImageHandler="true" @image-added="handleImageAdded" :customModules="customModulesForEditor" :editorOptions="editorSettings" :id="lang" :lang="lang" v-model="content[lang]" :editorToolbar="editorToolbar"></vue-editor>
                    <textarea v-else :name="name + '-' + lang" :id="name + '-' + lang" cols="30" rows="10" @input="handleInput" v-bind="parameters">{{ content[lang] }}</textarea>
                </div>
            </div>
        </fieldset>
        <span v-else>
            <vue-editor v-if="richEditor" useCustomImageHandler @image-added="handleImageAdded" :customModules="customModulesForEditor" :editorOptions="editorSettings" v-model="content" :editorToolbar="editorToolbar"></vue-editor>
            <textarea v-else :name="name" :id="name" cols="30" rows="10" @input="handleInput" v-bind="parameters">{{ content }}</textarea>
        </span>
    </block>

</template>

<script>
    import { VueEditor } from "vue2-editor";
    import ImageResize from "quill-image-resize-module";
    import VideoResize from 'quill-video-resize-module';
    import { htmlEditButton } from "quill-html-edit-button";
    import InputBase from './InputBase';
    import FullScreen from "lubart-vue-quill-fullscreen-module";

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
                customModulesForEditor: [
                    { alias: "imageResize", module: ImageResize },
                    { alias: "videoResize", module: VideoResize },
                    { alias: "htmlEditButton", module: htmlEditButton },
                    { alias: "fullScreen", module: FullScreen }
                ],
                editorSettings: {
                    modules: {
                        imageResize: {},
                        videoResize: {},
                        htmlEditButton: {
                            msg: "HTML Editor",
                            okText: "Apply",
                            buttonTitle: "HTML source"
                        },
                        fullScreen: {}
                    }
                },
                languages:{
                    en: 'English',
                    uk: 'Українська'
                },
                richEditor: this.parameters.richEditor === undefined ? true : !!this.parameters.richEditor
            };
        },

        methods:{
            handleImageAdded: function(file, Editor, cursorLocation, resetUploader) {
                // An example of using FormData
                // NOTE: Your key could be different such as:
                // formData.append('file', file)

                var formData = new FormData();
                formData.append("image", file);

                axios({
                    url: "/admin/upload-image",
                    method: "POST",
                    data: formData
                })
                    .then(result => {
                        let url = result.data.url; // Get url from response
                        Editor.insertEmbed(cursorLocation, "image", url);
                        resetUploader();
                    })
                    .catch(err => {
                        console.log(err);
                    });
            }
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

    ::backdrop {
        background-color: white !important;
    }

    .ql-html-overlayContainer .ql-html-popupContainer{
        border-radius: 0;
        background-color: #f5f5f5;
    }

    .ql-html-overlayContainer .ql-html-textContainer{
        padding: 0;
        height: 100%;
    }

    .ql-html-overlayContainer .ql-html-textArea{
        left: 0;
        width: 100%;
        height: calc(100% - 112px);
    }

    .ql-html-overlayContainer .ql-html-popupTitle{
        height: 40px;
        padding: 10px;
        font-weight: bold;
        font-style: normal;
    }

    .ql-html-overlayContainer .ql-html-buttonGroup{
        transform: scale(1.0);
        left: auto;
        right: 0;
        bottom: 0;
        padding: 20px;
    }

    .ql-html-overlayContainer .ql-html-buttonCancel, .ql-html-overlayContainer .ql-html-buttonOk {
        background-color: #77BAC0;
        color: #fff;
        padding: 0.5rem 1.5rem;
        font-weight: 700;
        border-width: 0;
        border-radius: 0.25rem;
        margin: 0 0.25rem;
    }

    .ql-html-overlayContainer .ql-html-buttonCancel:hover, .ql-html-overlayContainer .ql-html-buttonOk:hover{
        background-color: #98cdd2;
    }
</style>
