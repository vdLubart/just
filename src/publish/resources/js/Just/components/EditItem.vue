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
            }
        },

        methods:{
            confirmDeleting(){
                this.$root.$refs.settings.askConfirmation("Do you confirm deleting this item?", this.deleteItem);
            },

            deleteItem(){
                axios.post('/settings/' + this.$parent.itemName + '/delete', {
                    _token: this.$root.csrf,
                    id: _.last(this.editUrl.split('/'))
                }).then((response) => {
                    new Promise(resolve => {
                        return resolve(this.$root.navigate('/settings/' + this.$parent.itemName + '/list'));
                    })
                        .then(resolve => {
                            this.$root.$refs.settings.showSuccessMessage(response.data.message);
                        });
                })
                    .catch((error) => {
                        console.log("Cannot receive data.");
                        console.log(error.response);
                        this.$root.$refs.settings.showErrors(error.response.data);
                    });
            },

            activateItem(){

            },

            moveItemUp(){

            },

            moveItemDown(){

            }
        }
    }
</script>

<style scoped>

</style>