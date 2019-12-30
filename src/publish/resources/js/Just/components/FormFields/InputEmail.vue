<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <input type="email" :name="name" :id="name" class="input-component__text" :placeholder="placeholder" :value="content" @input="handleInput" v-bind="parameters" />
    </block>

</template>

<script>
    import { InputEmail } from 'lubart-vue-input-component';

    export default {
        name: "InputEmail",

        extends: InputEmail,

        props: {
            parameters: { type: Object }
        }
    }
</script>