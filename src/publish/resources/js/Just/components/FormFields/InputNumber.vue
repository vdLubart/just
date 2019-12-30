<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <input type="number" :name="name" :id="name" :value="content" class="input-component__text" @input="handleInput" v-bind="parameters"/>
    </block>

</template>

<script>
    import { InputNumber } from 'lubart-vue-input-component';

    export default {
        name: "InputNumber",

        extends: InputNumber,

        props: {
            parameters: { type: Object }
        }
    }
</script>