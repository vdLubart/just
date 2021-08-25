<script>
    import InputButton from './FormFields/InputButton';
    import InputText from './FormFields/InputText';
    import InputArea from './FormFields/InputArea';
    import InputDate from './FormFields/InputDate';
    import InputRadio from './FormFields/InputRadio';
    import RadioGroup from './FormFields/RadioGroup';
    import InputNumber from './FormFields/InputNumber';
    import InputHidden from './FormFields/InputHidden';
    import InputEmail from './FormFields/InputEmail';
    import InputCheckbox from './FormFields/InputCheckbox';
    import CheckboxGroup from './FormFields/CheckboxGroup';
    import HtmlBlock from './FormFields/HtmlBlock';
    import SelectVue from './FormFields/SelectVue';
    import InputFile from './FormFields/InputFile';
    import InputTime from './FormFields/InputTime';
    import InputPassword from './FormFields/InputPassword';

    export default {
        name: "FormField",

        components: {InputText, InputNumber, InputEmail, InputArea, InputDate, InputCheckbox, CheckboxGroup, InputRadio, RadioGroup, InputButton, InputHidden, SelectVue, HtmlBlock, InputFile, InputTime, InputPassword},

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
                'password': InputPassword,
                'email': InputEmail,
                'file': InputFile,
                'checkbox': _.isEmpty(this.element.options) ? InputCheckbox : CheckboxGroup,
                'radio': _.isEmpty(this.element.options) ? InputRadio : RadioGroup,
                'number': InputNumber,
                'select': SelectVue,
                'selectRange': null,
                'selectMonth': null,
                'hidden': InputHidden,
                'button': InputButton,
                'html': HtmlBlock,
                'date': InputDate,
                'time': InputTime
            };

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
