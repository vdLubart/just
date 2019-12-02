import Block from './Block';

export default {
    components: { Block },

    props: {
        name: {type: String},
        required: {type: Boolean, default: false},
        noWrap: {type: Boolean, default: false},
        withoutLabel: {type: Boolean, default: false},
        value: {type: String, default: ""},
        label: {type: String, default: ""}
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
    },

    methods: {
        handleInput (e) {
            this.content = e.target.value;
            this.$emit('input', this.content);
        }
    }
}