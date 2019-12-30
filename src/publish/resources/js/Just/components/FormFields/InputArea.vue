<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <textarea :name="name" :id="name" :cols="cols" :rows="rows" class="input-component__textarea" :value="content" @input="handleInput" v-bind="parameters"></textarea>
    </block>

</template>

<script>
    import { InputArea } from 'lubart-vue-input-component';

    export default {
        name: "InputArea",

        extends: InputArea,

        props: {
            parameters: { type: Object }
        }
    }
</script>