import { Fragment, useState } from "react";
import { getExtensionKeyIsValid } from "../../helper";
import InputComponent from "../forms/input/InputComponent";
import TextareaComponent from "../forms/textarea/TextareaComponent";
import SelectComponent from "../forms/select/SelectComponent";
import CheckboxComponent from "../forms/input/CheckboxComponent";

export const ExtensionPropertiesAccordion = (props) => {
    const [extensionProperties, setExtensionProperties] = useState(props.properties);

    const [isValid, setIsValid] = useState({});

    const categoryOptions = [
        "plugin",
        "module",
        "misc",
        "be",
        "fe",
        "services",
        "templates",
        "distribution",
        "example",
        "doc"
    ];

    const stateOptions = [
        "alpha",
        "beta",
        "stable",
        "experimental",
        "test",
    ];

    const targetTYPO3Versions = [
        "12.4"
    ];

    const handleValueChange = (field, value) => {
        updateExtensionPropertiesHandler(field, value);

        // Handle validation in the same method to keep code DRY
        if (["extensionKey", "vendorName", "name"].includes(field)) {
            const isValid = field === 'extensionKey' ? getExtensionKeyIsValid(value) : value.length >= 3;
            setIsValid(prevProps => ({
                ...prevProps,
                [field]: isValid
            }));
        }

        // If the changed field is 'emConf.targetVersion', also update the 'depends on' value
        if (field === 'emConf.targetVersion') {
            handleTargetTYPO3VersionChange(value);
        }
    };

    const handleTargetTYPO3VersionChange = (selectedVersion) => {
        const lines = (extensionProperties.emConf.dependsOn.split('\n').length === 1 && extensionProperties.emConf.dependsOn.split('\n')[0] === '') ? [] : extensionProperties.emConf.dependsOn.split('\n');
        let typo3LineFound = false;

        const generateTypo3Line = (version) => {
            return `typo3 => ${version}.0-${version}.99`;
        }

        const updatedLines = lines.map(line => {
            if (line.trim().startsWith('typo3')) {
                typo3LineFound = true;
                return generateTypo3Line(selectedVersion);
            }
            return line;
        });

        if (!typo3LineFound) {
            updatedLines.push(generateTypo3Line(selectedVersion));
        }

        const updatedDependsOnValue = updatedLines.join('\n');

        handleValueChange('emConf.dependsOn', updatedDependsOnValue);
    };

    const updateExtensionPropertiesHandler = (field, value) => {
        if (field.startsWith('emConf.')) {
            const emConfField = field.split('.')[1];
            setExtensionProperties(prevProps => ({
                ...prevProps,
                emConf: {
                    ...prevProps.emConf,
                    [emConfField]: value
                }
            }));
        } else {
            setExtensionProperties(prevProps => ({
                ...prevProps,
                [field]: value
            }));
        }
        props.updateExtensionPropertiesHandler(field, value);
    }

    return (
        <Fragment>
            <div className="panel panel-default">
                <div className="panel-heading">
                    <h3 className="fs-3 panel-title" id="heading-panel-properties">
                        <a href="#" data-bs-toggle="collapse" data-bs-target="#panel-properties" aria-expanded="true" aria-controls="panel-properties">
                            <span className="caret"></span>
                            <strong>Extension Properties</strong>
                        </a>
                    </h3>
                </div>
                <div id="panel-properties" className="accordion-collapse collapse show" aria-labelledby="heading-panel-properties"
                     data-bs-parent="#accordion-left-panel">
                    <div className="panel-body py-2">
                        <InputComponent
                            label="Extension name"
                            initialValue={props.properties.name}
                            identifier="extensionName"
                            validation={{isRequired: true, minLength: 2}}
                            onChange={(value) => {
                                handleValueChange('name', value)
                            }}
                        />
                        <InputComponent
                            label="Vendor Name"
                            initialValue={props.properties.vendorName}
                            identifier="vendorName"
                            validation={{isRequired: true, minLength: 2}}
                            onChange={(value) => {
                                handleValueChange('vendorName', value)
                            }}
                        />
                        <InputComponent
                            label="Extension key"
                            initialValue={props.properties.extensionKey}
                            identifier="extensionKey"
                            validation={{isRequired: true, minLength: 3, maxLength: 30}}
                            onChange={(value) => {
                                handleValueChange('extensionKey', value)
                            }}
                        />
                        <TextareaComponent
                            placeholder={"Please enter the description for this extension"}
                            label={"Extension Description"}
                            initialValue={props.properties.description}
                            identifier={"extensionDescription"}
                            validation={{isRequired: true, minLength: 5}}
                            onChange={(value) => {
                                handleValueChange('description', value)
                            }}
                        />
                        <SelectComponent
                            label="Category"
                            initialValue={props.properties.emConf.category}
                            identifier="extensionCategory"
                            options={categoryOptions}
                            validation={{isRequired: true}}
                            defaultValue="Please choose a category"
                            onChange={(value) => {
                                handleValueChange('emConf.category', value)
                            }}
                        />
                        <InputComponent
                            label="Extension Version"
                            initialValue={props.properties.emConf.version}
                            identifier="extensionVersion"
                            validation={{isRequired: true}}
                            onChange={(value) => {
                                handleValueChange('emConf.version', value)
                            }}
                        />
                        <SelectComponent
                            label="State"
                            initialValue={props.properties.emConf.state}
                            identifier="extensionState"
                            options={stateOptions}
                            validation={{isRequired: true}}
                            defaultValue="Please choose a state"
                            onChange={(value) => {
                                handleValueChange('emConf.state', value)
                            }}
                        />
                        <CheckboxComponent
                            label="Disable versioning"
                            identifier="extensionDisableVersioning"
                            checked={props.properties.emConf.disableVersioning}
                            onChange={(value) => {
                                handleValueChange('emConf.disableVersioning', value)
                            }}
                        />
                        <CheckboxComponent
                            label="Disable localization"
                            identifier="extensionDisableLocalization"
                            checked={props.properties.emConf.disableLocalization}
                            onChange={(value) => {
                                handleValueChange('emConf.disableLocalization', value)
                            }}
                        />
                        <CheckboxComponent
                            label="Generate documentation"
                            identifier="extensionGenerateDocumentation"
                            checked={props.properties.emConf.generateDocumentationTemplate}
                            onChange={(value) => {
                                handleValueChange('emConf.generateDocumentationTemplate', value)
                            }}
                        />
                        <CheckboxComponent
                            label="Generate git repository"
                            identifier="extensionGenerateGitRepository"
                            checked={props.properties.emConf.generateEmptyGitRepository}
                            onChange={(value) => {
                                handleValueChange('emConf.generateEmptyGitRepository', value)
                            }}
                        />
                        <CheckboxComponent
                            label="Generate editorconfig"
                            identifier="extensionGenerateEditorconfig"
                            checked={props.properties.emConf.generateEditorConfig}
                            onChange={(value) => {
                                handleValueChange('emConf.generateEditorConfig', value)
                            }}
                        />
                        <InputComponent
                            label="Source language for xliff files"
                            initialValue={props.properties.emConf.sourceLanguage}
                            identifier="extensionSourceLanguageXliffFiles"
                            validation={{isRequired: true}}
                            disabled
                            onChange={(value) => {
                                handleValueChange('emConf.sourceLanguage', value)
                            }}
                        />
                        <SelectComponent
                            label="Target TYPO3 version"
                            disabled
                            initialValue={props.properties.emConf.targetVersion}
                            identifier="extensionTargetTYPO3Version"
                            options={targetTYPO3Versions}
                            showEmptyValue={false}
                            // Hier wird nur der ausgewählte Wert (selectedVersion) übergeben
                            onChange={(selectedVersion) => {
                                handleTargetTYPO3VersionChange(selectedVersion);
                            }}
                        />
                        <TextareaComponent
                            placeholder={"typo3 => 12.4.0"}
                            label={"Depends on"}
                            initialValue={props.properties.emConf.dependsOn}
                            identifier={"extensionDependsOn"}
                            onChange={(value) => {
                                handleValueChange('emConf.dependsOn', value)
                            }}
                        />
                    </div>
                </div>
            </div>
        </Fragment>
    )
}
