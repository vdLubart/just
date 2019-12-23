export default class CreateLayout {

    constructor(){
        this.panelsAmount = 1;
        this.content = window.App.settings.content;
        this.panelTemplate = this.deepClone(this.content.groups[1]);
        this.numeratePanel(this.content.groups[1]);

        this.cleanRemoveButton();
    }

    addPanel(){
        this.content.groups.push(this.deepClone(this.panelTemplate));
        this.panelsAmount++;

        this.numeratePanel(this.content.groups[this.panelsAmount]);
    }

    cleanRemoveButton(){
        this.content.groups[1].elements.pop();
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
        let no = $(link).parent().parent().attr('id').substring(12);

        this.content.groups.forEach((group, key) => {
            if(group.elements[0].name === 'panel_'+no){
                this.content.groups.splice(key, 1);
            }
        });

        console.log(this.content.groups);
    }
}