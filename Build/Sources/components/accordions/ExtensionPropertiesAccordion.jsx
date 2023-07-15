import {Fragment, useState} from "react";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {getExtensionKeyIsValid} from "../../helper";
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
        "12.4",
        "13.0"
    ];

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

    // Change the depends on textarea depending on the target TYPO3 version
    const handleTargetTYPO3VersionChange = (e) => {
        const selectedVersion = e.target.value;
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

        updateExtensionPropertiesHandler('emConf.dependsOn', updatedDependsOnValue);
        updateExtensionPropertiesHandler('emConf.targetVersion', selectedVersion);
    };

    /* Validatin */
    const handleValidation = (field, value) => {
        if (field === 'extensionKey') {
            const isValidExtensionKey = getExtensionKeyIsValid(value)
            setIsValid(prevProps => ({
                ...prevProps,
                extensionKey: isValidExtensionKey
            }));
        }
        if(field === 'vendorName') {
            const isValidVendorName = value.length >= 3;
            setIsValid(prevProps => ({
                ...prevProps,
                vendorName: isValidVendorName
            }));
        }
        if(field === 'name') {
            const isValidExtensionName = value.length >= 3;
            setIsValid(prevProps => ({
                ...prevProps,
                name: isValidExtensionName
            }));
        }
    };

    return (
        <Fragment>
            <div className="panel panel-default">
                <div className="panel-heading">
                    <h3 className="panel-title" id="heading-panel-properties">
                        <a href="#" data-bs-toggle="collapse" data-bs-target="#panel-properties" aria-expanded="true" aria-controls="panel-properties">
                            <span className="caret"></span>
                            <strong>Extension Properties</strong>
                        </a>
                    </h3>
                </div>
                <div id="panel-properties" className="accordion-collapse collapse show" aria-labelledby="heading-panel-properties"
                     data-bs-parent="#accordion-left-panel">
                    <div className="panel-body">
                        <InputComponent
                            label="Extension name"
                            initialvalue={extensionProperties.extensionKey}
                            identifier="extensionName"
                            validation={{ isRequired: true, minLength: 2 }}
                            onChange={(value) => {
                                handleValidation('name', value)
                                updateExtensionPropertiesHandler('name', value);
                            }}
                        />
                        <InputComponent
                            label="Vendor Name"
                            initialvalue={extensionProperties.emConf.vendorName}
                            identifier="vendorName"
                            validation={{ isRequired: true, minLength: 2 }}
                            onChange={(value) => {
                                handleValidation('vendorName', value)
                                updateExtensionPropertiesHandler('vendorName', value);
                            }}
                        />
                        <InputComponent
                            label="Extension key"
                            initialvalue={extensionProperties.extensionKey}
                            identifier="extensionKey"
                            validation={{ isRequired: true, minLength: 5 }}
                            onChange={(value) => {
                                handleValidation('extensionKey', value)
                                updateExtensionPropertiesHandler('extensionKey', value);
                            }}
                        />
                        <TextareaComponent
                            placeholder={"Please enter the description for this extension"}
                            label={"Extension Description"}
                            initialvalue={extensionProperties.description}
                            identifier={"extensionDescription"}
                            onChange={(value) => {
                                updateExtensionPropertiesHandler('description', value);
                            }}
                        />
                        <SelectComponent
                            label="Category"
                            initialvalue={extensionProperties.emConf.category}
                            identifier="extensionCategory"
                            options={categoryOptions}
                            defaultValue="Please choose a category"
                            onChange={(value) => {
                                updateExtensionPropertiesHandler('emConf.category', value);
                            }}
                        />
                        <InputComponent
                            label="Extension Version"
                            initialvalue={extensionProperties.emConf.version}
                            identifier="extensionVersion"
                            onChange={(value) => {
                                updateExtensionPropertiesHandler('emConf.version', value);
                            }}
                        />
                        <SelectComponent
                            label="State"
                            initialvalue={extensionProperties.emConf.state}
                            identifier="extensionState"
                            options={stateOptions}
                            defaultValue="Please choose a state"
                            onChange={(value) => {
                                updateExtensionPropertiesHandler('emConf.state',value);
                            }}
                        />
                        <CheckboxComponent
                            label="Disable versioning"
                            identifier="extensionDisableVersioning"
                            checked={props.properties.emConf.disableVersioning}
                            onChange={(value) => {
                                updateExtensionPropertiesHandler('emConf.disableVersioning', value);
                            }}
                        />
                        <CheckboxComponent
                            label="Disable localization"
                            identifier="extensionDisableLocalization"
                            checked={props.properties.emConf.disableLocalization}
                            onChange={(value) => {
                                updateExtensionPropertiesHandler('emConf.disableLocalization', value);
                            }}
                        />
                       <CheckboxComponent
                            label="Generate documentation"
                            identifier="extensionGenerateDocumentation"
                            checked={props.properties.emConf.generateDocumentationTemplate}
                            onChange={(value) => {
                                updateExtensionPropertiesHandler('emConf.generateDocumentationTemplate', value);
                            }}
                        />
                        <CheckboxComponent
                            label="Generate git repository"
                            identifier="extensionGenerateGitRepository"
                            checked={props.properties.emConf.generateEmptyGitRepository}
                            onChange={(value) => {
                                updateExtensionPropertiesHandler('emConf.generateEmptyGitRepository', value);
                            }}
                        />
                        <CheckboxComponent
                            label="Generate editorconfig"
                            identifier="extensionGenerateEditorconfig"
                            checked={props.properties.emConf.generateEditorConfig}
                            onChange={(value) => {
                                updateExtensionPropertiesHandler('emConf.generateEditorConfig', value);
                            }}
                        />
                        <InputComponent
                            label="Source language for xliff files"
                            initialvalue={extensionProperties.emConf.sourceLanguage}
                            identifier="extensionSourceLanguageXliffFiles"
                            disabled
                            onChange={(value) => {
                                updateExtensionPropertiesHandler('emConf.sourceLanguage', value);
                            }}
                        />
                        <SelectComponent
                            label="Target TYPO3 version"
                            initialvalue={extensionProperties.emConf.targetVersion}
                            identifier="extensionTargetTYPO3Version"
                            options={targetTYPO3Versions}
                            defaultValue="Please choose a TYPO3 version"
                            onChange={(value) => {
                                handleTargetTYPO3VersionChange
                            }}
                        />
                        <TextareaComponent
                            placeholder={"typo3 => 12.4.0"}
                            label={"Depends on"}
                            initialvalue={extensionProperties.emConf.dependsOn}
                            identifier={"extensionDependsOn"}
                            onChange={(value) => {
                                updateExtensionPropertiesHandler('emConf.dependsOn', value);
                            }}
                        />
                    </div>
                </div>
            </div>
        </Fragment>
    )
}
