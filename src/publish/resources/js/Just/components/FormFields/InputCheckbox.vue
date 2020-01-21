<template>

    <div>
        <label class="checkbox-group__normalFont">
            <input type="checkbox" :name="name" :value="ticValue" :checked="content" @input="handleInput" v-bind="parameters"> {{ label }}
        </label>
    </div>

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