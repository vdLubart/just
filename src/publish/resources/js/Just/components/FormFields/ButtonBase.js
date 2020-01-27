import Block from './Block';

export default {
    components: { Block },

    props: {
        name: {type: String},
        wrap: {type: Boolean, default: false},
        value: {type: String, default: ""},
        parameters: {type: Object}
    },

    data(){
        return {
            content: this.value
        }
    },

    watch: {
        value(val){
            this.content = val;
        }
    }
}