<template>

    <div>
        <span v-if="isWaitingData">Loading data...</span>
        <div v-else>
            <form :action="content.action">

                <fieldset v-for="group in content.groups" v-bind="group.parameters">
                    <legend>{{ group.label }}</legend>

                    <field v-for="(element, key) in group.elements" :element="element" :key="key"></field>

                </fieldset>

                <field v-for="(element, key) in content.unGrouppedElements" :element="element" :key="key"></field>

            </form>
        </div>

    </div>
    
</template>

<script>
    import Field from './FormField';
    import CreateLayout from './FormLogic/Layout/CreateLayout';
    import {eventBus} from "../adminApp";

    export default {
        name: "Form",

        components: { Field },

        props: ['content', 'isWaitingData'],

        data() {
            return {
                logicClass: null,
                formData: {}
            }
        },

        mounted(){
            eventBus.$on('submitForm', this.submitForm);

            if(this.content.js != null){
                eval(this.content.js);
            }
        },

        beforeDestroy(){
            eventBus.$off('submitForm');
        },

        methods: {
            getClassInstance(className){
                if(this.logicClass != null){
                    return this.logicClass;
                }

                switch (className){
                    case 'CreateLayout':
                        this.logicClass = new CreateLayout();
                        break;
                }

                return this.logicClass;
            },

            submitForm(){
                this.content.unGrouppedElements.forEach(element=>this.elementValue(element));

                this.content.groups.forEach(group => {
                    group.elements.forEach(element=>this.elementValue(element))
                });

                axios({
                    method: this.content.method,
                    url: this.content.action,
                    data: this.formData
                })
                    .then((response) => {
                        if(response.data.redirect !== null) {
                            this.$root.navigate(response.data.redirect).then(()=>{
                                this.$parent.$parent.showSuccessMessage(response.data.message);

                                setTimeout(this.$parent.$parent.resetAlert, 5000);
                            });
                        }

                        this.$parent.$parent.showSuccessMessage(response.data.message);
                    })
                    .catch((response) => {
                        console.error("Cannot send data.");
                        console.error(response.response);
                        this.$parent.$parent.showErrors(response.response.data);
                    });
            },

            elementValue(element){
                if(!_.includes(['submit', 'html', 'button'], element.type)){
                    this.formData[element.name] = element.value;
                }
            }
        }
    }
</script>

<style scoped>

</style>