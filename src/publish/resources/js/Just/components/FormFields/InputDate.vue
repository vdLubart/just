<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <date-picker lang="en" :inputName="name" :format="format" :placeholder="placeholder" v-model="content" @input="handleInput" v-bind="parameters"></date-picker>
    </block>

</template>

<script>
    import { InputDate } from 'lubart-vue-input-component';

    export default {
        name: "InputDate",

        extends: InputDate,

        props: {
            parameters: { type: Object }
        }
    }
</script>