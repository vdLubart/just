export default class CreateLayout {

    constructor(){
        this.panelsAmount = 1;
        this.content = window.App.settings.content;
        if(this.content.groups[0].elements[0].value == 0) {
            this.panelTemplate = this.deepClone(_.last(this.content.groups));
            this.numeratePanel(_.last(this.content.groups));
        }

        this.cleanRemoveButton();
    }

    addPanel(){
        this.content.groups.push(this.deepClone(this.panelTemplate));
        this.panelsAmount++;

        this.numeratePanel(this.content.groups[_.last(_.keys(this.content.groups))]);
    }

    cleanRemoveButton(){
        // detect if layout_id value is bigger then 0
        if(this.content.groups[0].elements[0].value > 0){
            this.content.groups.pop();
        }
        else{
            _.last(this.content.groups).elements.pop();
        }
    }

    deepClone(object){
        return JSON.parse(JSON.stringify(object));
    }

    numeratePanel(panel){
        Object.keys(panel.elements).forEach(key=>{
            panel.elements[key].name += '_' + this.panelsAmount;
        });
    }

    removePanel(link){
        let no = link.parentElement.parentElement.getAttribute('id').substring(12);

        this.content.groups.forEach((group, key) => {
            if(group.elements[0].name === 'panel_'+no){
                this.content.groups.splice(key, 1);
            }
        });
    }
}