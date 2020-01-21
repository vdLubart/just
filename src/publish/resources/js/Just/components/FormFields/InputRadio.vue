<template>

    <div>
        <label class="radio-group__normalFont">
            <input type="radio" :name="name" :value="value" :checked="content" @input="handleInput" v-bind="parameters"> {{ label }}
        </label>
    </div>

</template>

<script>
    import InputBase from './InputBase';

    export default {
        name: "InputRadio",

        extends: InputBase,

        props: {
            parameters: { type: Object }
        }
    }
</script>

<style scoped>

    .radio-group__normalFont{
        font-weight: normal;
    }

</style>