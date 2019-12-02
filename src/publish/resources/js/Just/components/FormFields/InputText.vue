<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <input type="text" :name="name" :id="name" class="input-component__text" :placeholder="placeholder" :value="content" @input="handleInput" />
    </block>

</template>

<script>
    import InputBase from './InputBase';

    export default {
        name: "InputText",

        extends: InputBase,

        props: {
            placeholder: {type: String, default: ""},
        }
    }
</script>

<style scoped>

    .input-component__text{
        border: 1px solid #dbdbdb;
        border-radius: 4px;
        padding: 5px;
    }

    .input-component__text:focus{
        border: 1px solid #77BAC0;
    }

</style>