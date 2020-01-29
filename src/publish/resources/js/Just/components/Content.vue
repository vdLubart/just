<script>
    import Form from './Form';
    import Crop from './Crop';
    import List from './List';
    import ItemList from './ItemList';
    import {eventBus} from "../adminApp";

    export default {
        name: "Content",

        components: {Form, Crop, List, ItemList},

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

        beforeDestroy(){
            eventBus.$off('contentReceived');
        },

        render(createElement){
            let inputMap = {
                'form': Form,
                'crop': Crop,
                'list': List,
                'items': ItemList
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