<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <label class="checkbox-group__normalFont">
            <input type="checkbox" :name="name" :value="ticValue" :checked="content" @input="handleInput" v-bind="parameters"> {{ ticLabel }}
        </label>
    </block>

</template>

<script>
    import { InputCheckbox } from 'lubart-vue-input-component';

    export default {
        name: "InputCheckbox",

        extends: InputCheckbox,

        props: {
            parameters: { type: Object }
        }
    }
</script>