<template>

    <div>
        <span v-if="isWaitingData">Loading data...</span>
        <div v-else>
            <form :action="content.action">

                <fieldset v-for="group in content.groups" v-bind="group.parameters">
                    <legend>{{ group.label }}</legend>

                    <field v-for="(element, key) in group.elements" :element="element" :key="key" :ref="element.name"></field>

                </fieldset>

                <field v-for="(element, key) in content.unGrouppedElements" :element="element" :key="key" :ref="element.name"></field>

            </form>
        </div>

    </div>

</template>

<script>
    import Field from './FormField';
    import CreateLayout from './FormLogic/Layout/CreateLayout';
    import CustomizeBlock from './FormLogic/Block/CustomizeBlock';
    import {eventBus} from "../adminApp";

    export default {
        name: "Form",

        components: { Field },

        props: ['content', 'isWaitingData'],

        data() {
            return {
                logicClass: null,
                formData: new FormData()
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
                    case 'CustomizeBlock':
                        this.logicClass = new CustomizeBlock();
                        break;
                }

                return this.logicClass;
            },

            collectFormData(){
                this.content.unGrouppedElements.forEach(element=>this.elementValue(element));

                this.content.groups.forEach(group => {
                    group.elements.forEach(element=>this.elementValue(element))
                });

                return this.formData;
            },

            submitForm(){
                this.collectFormData();

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
                if(!_.includes(['submit', 'file'], element.type)){
                    let value = element.value;

                    switch(true){
                        case _.isNull(element.value):
                            value = '';
                            break;
                        case _.isObject(element.value):
                            value = JSON.stringify(element.value);
                            break;
                    }

                    this.formData.append(element.name, value);
                }

                if(element.type == 'file' && !_.isNull(element.value)){
                    this.formData.append(element.name, element.value);
                }
            }
        }
    }
</script>

<style scoped>

</style>
