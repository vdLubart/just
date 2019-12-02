<script>
    import InputText from './FormFields/InputText';
    import InputNumber from './FormFields/InputNumber';
    import InputEmail from './FormFields/InputEmail';
    import InputArea from './FormFields/InputArea';
    import InputDate from './FormFields/InputDate';
    import InputCheckbox from './FormFields/InputCheckbox';
    import CheckboxGroup from './FormFields/CheckboxGroup';
    import RadioGroup from './FormFields/RadioGroup';
    import InputButton from './FormFields/InputButton';
    import InputHidden from './FormFields/InputHidden';

    export default {
        name: "FormField",

        components: {InputText, InputNumber, InputEmail, InputArea, InputDate, InputCheckbox, CheckboxGroup, RadioGroup, InputButton, InputHidden},

        props:{
            "element": {type: Object}
        },

        render(createElement){
            let inputMap = {
                'text': InputText,
                'textarea': InputArea,
                'password': null,
                'email': InputEmail,
                'file': null,
                'checkbox': CheckboxGroup,
                'radio': RadioGroup,
                'number': InputNumber,
                'select': null,
                'selectRange': null,
                'selectMonth': null,
                'hidden': InputHidden,
                'button': InputButton,
                'html': null,
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
                    value: this.element.value
                }
            });
        }
    }
</script>