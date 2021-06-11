<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel" >
        <fieldset v-if="parameters.translate === true">
            <div v-for="(label, lang) in languages" class="input-text-component__translate-container">
                <div>{{ label }}</div>
                <div>
                    <lubart-text :name="name + '-' + lang" :placeholder="placeholder" v-model="content[lang]" @input="handleInput" :no-wrap="true" v-bind="parameters"></lubart-text>
                </div>
            </div>
        </fieldset>
        <lubart-text v-else :name="name" :placeholder="placeholder" v-model="content" @input="handleInput" :no-wrap="true" v-bind="parameters"></lubart-text>
    </block>

</template>

<script>
    import { InputText as lubartText} from 'lubart-vue-input-component';
    import InputBase from './InputBase';

    export default {
        name: "InputText",

        extends: InputBase,

        components: {lubartText},

        props: {
            parameters: { type: Object },
            placeholder: {type: String},
            value: {}
        },

        data(){
            return {
                content: this.value === null ? (this.parameters.translate ? {} : '') : this.value,
                languages:{
                    en: 'English',
                    uk: 'Українська'
                }
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

        methods: {
            handleInput (e) {
                this.$emit('input', this.content);
            }
        },

        watch: {
            content(val){
                this.$emit('input', val);
            }
        }
    }
</script>
