import Block from './Block';

export default {
    components: { Block },

    props: {
        name: {type: String},
        wrap: {type: Boolean, default: false},
        label: {type: String, default: ""}
    },

    data(){
        return {
            content: this.label
        }
    },

    watch: {
        label(val){
            this.content = val;
        }
    }
}