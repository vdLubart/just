<script>
    import DynamicForm from './Form';

    export default {
        name: "Content",

        components: {DynamicForm},

        props:{
            "type": {type: String, default: "list"}
        },

        render(createElement){
            let inputMap = {
                'form': DynamicForm
            }

            return createElement(inputMap[this.type]);
        }
    }
</script>