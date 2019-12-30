<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <div v-for="(radioLabel, radioValue) in radioButtons">
            <label class="radio-group__normalFont">
                <input type="radio" :name="name" :value="radioValue" :checked="radioValue === value" @input="handleInput" v-bind="parameters"> {{ radioLabel }}
            </label>
        </div>
    </block>

</template>

<script>
    import { RadioGroup } from 'lubart-vue-input-component';

    export default {
        name: "RadioGroup",

        extends: RadioGroup,

        props: {
            parameters: { type: Object }
        }
    }
</script>