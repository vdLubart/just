<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel" >
        <input type="file" :name="name" :id="name" :ref="'file-' + name" class="input-component__file" v-on:change="handleFileUpload" v-bind="parameters"/>
    </block>
    
</template>

<script>
    import InputBase from './InputBase';

    export default {
        name: "InputFile",

        extends: InputBase,

        props: {
            value: {type: File},
        },

        methods:{
            handleFileUpload(e){
                this.content = this.$refs['file-' + this.name].files[0];
                this.$emit('input', this.content);
            }
        }
    }
</script>

<style scoped>

</style>