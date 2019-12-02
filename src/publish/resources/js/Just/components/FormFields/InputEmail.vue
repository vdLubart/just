<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <input type="email" :name="name" :id="name" class="input-component__text" :placeholder="placeholder" :value="content" @input="handleInput" />
    </block>

</template>

<script>
    import InputText from './InputText';

    export default {
        name: "InputEmail",

        extends: InputText
    }
</script>