<script>
    import _ from 'lodash';

    export default {
        name: "Block",

        props: {
            id: {type: String},
            required: {type: Boolean, default: false},
            label: {type: String, default: ""},
            withoutLabel: {type: Boolean, default: false},
            noWrap: {type: Boolean, default: false}
        },

        data(){
            return {
                blockId: this.id + "_block",
                blockClass: this.class + " input-component__block",
                inputLabel: this.label
            }
        },

        created(){
            if(this.label == "" && this.id !== undefined){
                this.inputLabel = _.startCase(this.id);
            }
        },

        render(createElement){
            if(!this.noWrap){
                let children = [];

                if(!this.withoutLabel){
                    let labelContent = [this.inputLabel];

                    if(this.required){
                        labelContent = _.concat(labelContent, createElement('span', { class: "input-component__block_required" }, ' *'));
                    }

                    labelContent = _.concat(labelContent, createElement('br'));

                    children = _.concat(children, createElement('label', { attrs: { for: this.id} }, labelContent));
                }

                children = _.concat(children, this.$slots.default);

                return createElement('div', { attrs: {id: this.blockId}, class: "input-component__block" },
                    children
                );
            }
            else{
                return this.$slots.default;
            }
        }
    }
</script>

<style scoped>

    .input-component__block{
        margin-bottom: 20px;
    }

    .input-component__block_required{
        color: red;
    }

</style>