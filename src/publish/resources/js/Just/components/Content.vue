<script>
    import Form from './Form';
    import ListView from './ListView';
    import {eventBus} from "../adminApp";

    export default {
        name: "Content",

        components: {Form, ListView},

        props:{
            "type": {type: String, default: "list"}     // values: list, form, items
        },

        data(){
            return {
                content: null,
                isWaitingData: true,
                key: 0
            }
        },

        created(){
            eventBus.$on('contentReceived', content => {
                this.content = content;
                this.isWaitingData = false;
                this.key++;
            });
        },

        render(createElement){
            let inputMap = {
                'form': Form,
                'list': ListView
            }

            return createElement(inputMap[this.type], {
                attrs: {
                    content: this.content,
                    isWaitingData: this.isWaitingData,
                    class: 'settingsContent'
                },
                key: this.key
            });
        }
    }
</script>