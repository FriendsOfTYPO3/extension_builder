import CheckboxComponent from "./input/Checkbox";
import TextareaComponent from "./textarea/TextareaComponent";
import SelectComponent from './select/SelectComponent';
import InputComponent from "./input/InputComponent";

export const FormWrapperComponent = () => {
    return (
        <>
            <CheckboxComponent
                label="Ich akzeptiere die Nutzungsbedingungen"
                checked={true}
                onChange={(isChecked) => console.log('Checkbox geändert:', isChecked)}
            />
            <InputComponent />
            <TextareaComponent />
            <SelectComponent />
        </>
    )
}
