<template>

    <div>
        <a href="" @click.prevent="confirmDeleting" :title="__('actions.delete')">
            <i class="fa fa-trash-alt"></i>
        </a>
        <a href="" v-if="activating" @click.prevent="activateItem" :title="!inactive ? __('actions.deactivate'):__('actions.activate')">
            <i :class="'fa fa-' + (!inactive ? 'eye-slash' : 'eye')"></i>
        </a>
        <a href="" v-if="moving" @click.prevent="moveItemUp" :title="__('actions.moveUp')">
            <i class="fa fa-arrow-up"></i>
        </a>
        <a href="" v-if="moving" @click.prevent="moveItemDown" :title="__('actions.moveDown')">
            <i class="fa fa-arrow-down"></i>
        </a>
        <slink :href="'/settings/' + editUrl" :title="__('actions.edit')">
            <i class="fa fa-pencil-alt"></i>
        </slink>
    </div>

</template>

<script>
    import Link from './Link';

    export default {
        name: "EditItem",

        components: {Link},

        props:{
            activating: {type: Boolean, default: true},
            inactive: {type: Boolean, default: false},
            moving: {type: Boolean, default: true},
            url: {type: String}
        },

        data(){
            return {
                editUrl: this.url,
                postData: {
                    _token: this.$root.csrf
                },
                postUri: ""
            }
        },

        created(){
            let module = this.url.split('/')[0];

            if(module === 'block'){
                this.postData.block_id = this.url.split('/')[1];
            }

            this.postData.id = _.last(this.url.split('/'));
        },

        methods:{
            confirmDeleting(){
                this.$root.$refs.settings.askConfirmation("Do you confirm deleting this item?", this.deleteItem);
            },

            deleteItem(){
                if(this.$root.settings.responseParameters.blockType){
                    this.postUri = '/settings/block/item/delete';
                }
                else if(this.$root.settings.responseParameters.addonType){
                    this.postUri = '/settings/add-on-option/' + this.$root.settings.responseParameters.addonType + '/option/delete';
                }
                else {
                    this.postUri = '/settings/' + this.$parent.itemName + '/delete';
                }
                this.fireAction();
            },

            activateItem(){
                if(this.$root.settings.responseParameters.blockType){
                    this.postUri = '/settings/block/item/' + (!this.inactive ? 'deactivate':'activate');
                }
                else if(this.$root.settings.responseParameters.addonType){
                    this.postUri = '/settings/add-on-option/' + this.$root.settings.responseParameters.addonType + '/option/' + (!this.inactive ? 'deactivate':'activate');
                }
                else {
                    this.postUri = '/settings/' + this.$parent.itemName + '/' + (!this.inactive ? 'deactivate':'activate');
                }
                this.fireAction();
            },

            moveItemUp(){
                if(this.$root.settings.responseParameters.blockType){
                    this.postUri = '/settings/block/item/moveup';
                }
                else if(this.$root.settings.responseParameters.addonType){
                    this.postUri = '/settings/add-on-option/' + this.$root.settings.responseParameters.addonType + '/option/moveup';
                }
                else {
                    this.postUri = '/settings/' + this.$parent.itemName + '/moveup';
                }
                this.fireAction();
            },

            moveItemDown(){
                if(this.$root.settings.responseParameters.blockType){
                    this.postUri = '/settings/block/item/movedown';
                }
                else if(this.$root.settings.responseParameters.addonType){
                    this.postUri = '/settings/add-on-option/' + this.$root.settings.responseParameters.addonType + '/option/movedown';
                }
                else {
                    this.postUri = '/settings/' + this.$parent.itemName + '/movedown';
                }
                this.fireAction();
            },

            fireAction(){
                axios.post(this.postUri, this.postData)
                    .then((response) => {
                        this.$root.navigate(response.data.redirect).then(()=>{
                            this.$root.$refs.settings.showSuccessMessage(response.data.message);

                            setTimeout(this.$root.$refs.settings.resetAlert, 5000);
                        });
                    })
                    .catch((error) => {
                        console.log("Cannot receive data.");
                        console.log(error.response);
                        this.$root.$refs.settings.showErrors(error.response.data);
                    });
            }
        }
    }
</script>

<style scoped>

</style>
