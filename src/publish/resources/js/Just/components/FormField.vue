<script>
    import { InputText, InputArea, InputDate, RadioGroup, InputNumber, InputCheckbox, InputEmail, InputButton } from 'lubart-vue-input-component';
    import InputHidden from './FormFields/InputHidden';
    import HtmlBlock from './FormFields/HtmlBlock';
    import SelectChosen from './FormFields/SelectChosen';

    export default {
        name: "FormField",

        components: {InputText, InputNumber, InputEmail, InputArea, InputDate, InputCheckbox, RadioGroup, InputButton, InputHidden, SelectChosen, HtmlBlock},

        props:{
            "element": {type: Object}
        },

        methods:{
            updateContent(value){
                this.element.value = value;
            }
        },

        render(createElement){
            let inputMap = {
                'text': InputText,
                'textarea': InputArea,
                'password': null,
                'email': InputEmail,
                'file': null,
                'checkbox': InputCheckbox,
                'radio': RadioGroup,
                'number': InputNumber,
                'select': SelectChosen,
                'selectRange': null,
                'selectMonth': null,
                'hidden': InputHidden,
                'button': InputButton,
                'html': HtmlBlock,
                'date': InputDate,
                'time': null
            }

            if(inputMap[this.type] === null){
                return createElement('span');
            }

            return createElement(inputMap[this.element.type], {
                attrs: {
                    label: this.element.label,
                    name: this.element.name,
                    required: this.element.isObligatory,
                    value: this.element.value,
                    parameters: this.element.parameters
                },
                on: {
                    input: this.updateContent
                }
            });
        }
    }
</script>