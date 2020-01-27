export default class CustomizeBlock {

    constructor(){
        this.cropDimensionsElement = this.deepClone(this.findGroup('cropGroup').elements[1]);
        this.checkImageSizesElement = this.deepClone(this.findGroup('sizeGroup').elements[2]);

        if(!this.findGroup('cropGroup').elements[0].checked) {
            this.checkCropDimensionsVisibility();
        }

        if(!this.findGroup('sizeGroup').elements[0].checked) {
            this.checkImageSizesVisibility();
        }
    }

    deepClone(object){
        return JSON.parse(JSON.stringify(object));
    }

    findGroup(groupName){
        let foundGroup = null;
        window.App.settings.content.groups.forEach(group => {
            if(group.name == groupName){
                foundGroup = group;
            }
        });

        return foundGroup;
    }

    showCropDimensions(){
        this.findGroup('cropGroup').elements.push(this.cropDimensionsElement);
    }

    hideCropDimensions(){
        this.findGroup('cropGroup').elements.splice(1,1);
    }

    checkCropDimensionsVisibility(){
        this.findGroup('cropGroup').elements[0].value ? this.showCropDimensions() : this.hideCropDimensions();
    }

    showImageSizes(){
        this.findGroup('sizeGroup').elements.push(this.checkImageSizesElement);
    }

    hideImageSizes(){
        this.findGroup('sizeGroup').elements.splice(2, 1);
    }

    checkImageSizesVisibility(){
        this.findGroup('sizeGroup').elements[0].value ? this.showImageSizes() : this.hideImageSizes();
    }

}