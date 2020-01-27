<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <label v-for="(ticLabel, ticValue) in options" class="checkbox-group__normalFont">
            <input type="checkbox" :name="name" :value="ticValue" :checked="content.indexOf(ticValue) !== -1" @input="handleInput" v-bind="parameters"> {{ ticLabel }}
        </label>
    </block>

</template>

<script>
    import RadioGroup from './RadioGroup';

    export default {
        name: "CheckboxGroup",

        extends: RadioGroup,

        props:{
            value: {type: String|Boolean}
        },

        created() {
            this.content = _.isEmpty(this.value) || _.isBoolean(this.value) ? [] : JSON.parse(this.value);
        },

        methods:{
            handleInput (e) {
                let index = this.content.indexOf(e.target.value);

                if(!e.target.checked){
                    if (index !== -1) this.content.splice(index, 1);
                }
                else{
                    if (index === -1) this.content.push(e.target.value);
                }

                this.$emit('input', this.content);
            }
        },
    }
</script>

<style scoped>

    .checkbox-group__normalFont{
        display: block;
        font-weight: normal;
    }

</style>