<template>

    <div>
        <span v-if="isWaitingData">Loading data...</span>
        <form v-else action="" style="display: flex; flex-wrap: wrap">

            <fieldset v-for="group in content.groups" v-bind="group.parameters">
                <legend>{{ group.label }}</legend>

                <field v-for="(element, key) in group.elements" :element="element" :key="key"></field>

            </fieldset>

            <field v-for="(element, key) in content.unGrouppedElements" :element="element" :key="key"></field>

        </form>

        <footer>
            <button>Save</button>
        </footer>
    </div>
    
</template>

<script>
    import Field from './FormField';

    import CreateLayout from './FormLogic/Layout/CreateLayout';

    export default {
        name: "Form",

        components: { Field },

        props: ['content', 'isWaitingData'],

        data() {
            return {
                logicClass: null
            }
        },

        created(){
            if(this.content.js != null){
                eval(this.content.js);
            }
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
            }
        }
    }
</script>

<style scoped>

    footer{
        display: flex;
        justify-content: end;
    }

</style>