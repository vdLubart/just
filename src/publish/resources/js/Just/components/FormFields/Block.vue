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
                inputLabel: this.label,
                isHidden: false,
                hideTitle: this.hideActionTitle()
            }
        },

        created(){
            if(this.label == "" && this.id !== undefined){
                this.inputLabel = _.startCase(this.id);
            }

            if(!this.$parent.$parent.element.isObligatory && this.isEmpty(this.$parent.content)) {
                this.toggleVisibility();
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

                    children = _.concat(children, createElement('label', { attrs: { for: this.id} }, labelContent));

                    children = _.concat(children, createElement('a', { class: 'input-component__hide-switcher', on: { click: this.toggleVisibility}}, [createElement('i', {class: 'fa fa-eye'+(this.isHidden?'':'-slash')}), ' ' + this.hideTitle]));

                    children = _.concat(children, createElement('br'));
                }

                let slotContainer = createElement('span', {class: this.isHidden ? "input-component__block_hidden" : ""}, this.$slots.default);

                children = _.concat(children, slotContainer);

                return createElement('div', { attrs: {id: this.blockId}, class: "input-component__block" },
                    children
                );
            }
            else{
                return this.$slots.default;
            }
        },

        methods:{
            toggleVisibility(){
                this.isHidden = !this.isHidden;
                this.hideTitle = this.hideActionTitle();
            },

            hideActionTitle(){
                return this.isHidden ? this.__('actions.showField') : this.__('actions.hideField');
            },

            isEmpty(content){
                if(_.isNull(content)){
                    return true;
                }

                if(_.isObject(content)){
                    let empty = true;
                    Object.values(content).forEach(item => {
                        if(item != ''){
                            empty = false;
                        }
                    });

                    return empty;
                }

                return _.isEmpty(content);
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

    .input-component__block_hidden{
        display: none;
    }

    .input-component__hide-switcher{
        margin-left: 20px;
    }

</style>