<script>
    import DynamicForm from './Form';
    import ListView from './ListView';

    export default {
        name: "Content",

        components: {DynamicForm, ListView},

        props:{
            "type": {type: String, default: "list"}
        },

        render(createElement){
            let inputMap = {
                'form': DynamicForm,
                'list': ListView
            }

            return createElement(inputMap[this.type]);
        }
    }
</script>