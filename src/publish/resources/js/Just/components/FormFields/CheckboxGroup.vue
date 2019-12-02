<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <div v-for="(ticLabel, ticValue) in checkboxes">
            <label class="checkbox-group__normalFont">
                <input type="checkbox" :name="name" :value="ticValue" :checked="content[ticValue]" @input="handleInput"> {{ ticLabel }}
            </label>
        </div>
    </block>

</template>

<script>
    import InputBase from './InputBase';

    export default {
        name: "CheckboxGroup",

        extends: InputBase,

        props: {
            checkboxes: {type: Object},                      // format should be {"tic_value":"tic label"}
            value: {type: Array, default: function () { return [] }}              // format should be ["tic_value"]
        },

        data(){
            let ticStatus = {};

            Object.keys(this.checkboxes).forEach((value) => {
                ticStatus[value] = false;
            });

            this.value.forEach((value) => {
                ticStatus[value] = true;
            });

            return {
                content: ticStatus,
                checkedValues: [],
            }
        },

        methods: {
            handleInput (e) {
                this.content[e.target.value] = e.target.checked;
                this.checkedValues = this.checkedTics();
                this.$emit('input', this.checkedValues)
            },

            checkedTics(){
                let checkedTics = [];
                Object.keys(this.content).forEach((tic) => {
                    if(this.content[tic]) {
                        checkedTics.push(tic);
                    }
                });

                return checkedTics;
            }
        }
    }
</script>

<style scoped>

    .checkbox-group__normalFont{
        font-weight: normal;
    }

</style>