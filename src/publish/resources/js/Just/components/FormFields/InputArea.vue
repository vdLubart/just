<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <textarea :name="name" :id="name" :cols="cols" :rows="rows" :placeholder="placeholder" class="input-component__textarea" :value="content" @input="handleInput"></textarea>
    </block>

</template>

<script>
    import InputBase from './InputBase';

    export default {
        name: "InputArea",

        extends: InputBase,

        props: {
            placeholder: {type: String, default: ""},
            rows: {type: Number, default: 5},
            cols: {type: Number, default: 40}
        }
    }
</script>

<style scoped>

    .input-component__textarea{
        border: 1px solid #dbdbdb;
        border-radius: 4px;
        padding: 5px;
    }

    .input-component__textarea:focus{
        border: 1px solid #77BAC0;
    }

</style>