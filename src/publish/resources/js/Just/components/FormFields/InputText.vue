<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel" >
        <fieldset v-if="parameters.translate === true">
            <div v-for="(label, lang) in languages" class="input-text-component__translate-container">
                <div>{{ label }}</div>
                <div>
                    <input type="text" :name="name + '-' + lang" :id="name + '-' + lang" :lang="lang" class="input-component__text" :placeholder="placeholder" :value="content[lang]" @input="handleInput" v-bind="parameters"/>
                </div>
            </div>
        </fieldset>
        <input v-else type="text" :name="name" :id="name" class="input-component__text" :placeholder="placeholder" :value="content" @input="handleInput" v-bind="parameters"/>
    </block>

</template>

<script>
    import InputBase from './InputBase';

    export default {
        name: "InputText",

        extends: InputBase,

        props: {
            parameters: { type: Object },
            placeholder: {type: String},
            value: {}
        },

        data(){
            return {
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
                if(this.parameters.translate === true){
                    this.content[e.target.lang] = e.target.value;
                }
                else {
                    this.content = e.target.value;
                }

                this.$emit('input', this.content);
            }
        }
    }
</script>