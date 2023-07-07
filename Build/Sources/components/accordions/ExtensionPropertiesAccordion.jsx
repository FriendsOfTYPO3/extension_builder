import {Fragment, useState} from "react";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {getExtensionKeyIsValid} from "../../helper";

export const ExtensionPropertiesAccordion = (props) => {
    const [extensionProperties, setExtensionProperties] = useState(props.properties);
    const [isValid, setIsValid] = useState({});

    const categoryOptions = [
        { key: "plugin", value: "Frontend plugins" },
        { key: "module", value: "Backend modules" },
        { key: "misc", value: "Miscellaneous" },
        { key: "be", value: "Backend" },
        { key: "fe", value: "Frontend" },
        { key: "services", value: "Service" },
        { key: "templates", value: "Templates" },
        { key: "distribution", value: "Distribution" },
        { key: "example", value: "Examples" },
        { key: "doc", value: "Documentation" }
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
                        <div className="mb-2">
                            <label htmlFor="extensionName"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-signature" /></span>Extension Name</label>
                            <input
                                type="text"
                                className={`form-control form-control-sm ${isValid['name'] ? 'is-valid' : 'is-invalid'}`}
                                placeholder="Extension Name"
                                aria-label="Extension Name"
                                aria-describedby="basic-addon1"
                                value={extensionProperties.name}
                                onChange={(e) => {
                                    handleValidation('name', e.target.value)
                                    updateExtensionPropertiesHandler('name', e.target.value);
                                }}
                            />
                            <div id="validationVendorNameFeedback" className="invalid-feedback">
                                Please select a valid vendor name.
                            </div>
                        </div>
                        <div className="mb-2">
                            <label htmlFor="extensionVendorName"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-signature" /></span>Vendor Name</label>
                            <input
                                type="text"
                                className={`form-control form-control-sm ${isValid['vendorName'] ? 'is-valid' : 'is-invalid'}`}
                                placeholder="Vendor Name"
                                aria-label="Vendor Name"
                                aria-describedby="basic-addon1"
                                value={extensionProperties.emConf.vendorName}
                                onChange={(e) => {
                                    handleValidation('vendorName', e.target.value)
                                    updateExtensionPropertiesHandler('vendorName', e.target.value);
                                }}
                            />
                            <div id="validationVendorNameFeedback" className="invalid-feedback">
                                Please select a valid vendor name.
                            </div>
                        </div>
                        <div className="mb-2">
                            <label htmlFor="extensionKey"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-key" /></span>Extension key</label>
                            <input
                                type="text"
                                className={`form-control form-control-sm ${isValid['extensionKey'] ? 'is-valid' : 'is-invalid'}`}
                                placeholder="Extension key"
                                aria-label="Extension key"
                                aria-describedby="basic-addon1"
                                value={extensionProperties.extensionKey}
                                onChange={(e) => {
                                    handleValidation('extensionKey', e.target.value)
                                    updateExtensionPropertiesHandler('extensionKey', e.target.value);
                                }}
                            />
                            <div id="validationExtensionKeyFeedback" className="invalid-feedback">
                                Please select a valid extension key.
                            </div>
                        </div>
                        <div className="mb-2">
                            <label htmlFor="exampleFormControlTextarea1" className="form-label"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-question" /></span>Extension Description</label>
                            <textarea
                                type="text"
                                className="form-control form-control-sm"
                                id="exampleFormControlTextarea1"
                                placeholder="Please enter the description for this extension"
                                value={extensionProperties.description}
                                onChange={(e) => {
                                    updateExtensionPropertiesHandler('description', e.target.value);
                                }}
                                rows="5" />
                        </div>
                        <div className="mb-2">
                            <label htmlFor="extensionCategory">
                                <span className="me-2">
                                    <FontAwesomeIcon icon="fa-solid fa-tag" />
                                </span>Category
                            </label>
                            <select
                                className="form-select"
                                aria-label="Category"
                                onChange={(e) => {
                                    updateExtensionPropertiesHandler('emConf.category', e.target.value);
                                }}
                            >
                                <option>Please choose the category</option>
                                {
                                    categoryOptions.map((category, index) => {
                                        return (
                                            <option key={index} value={category.key}>{category.value}</option>
                                        )
                                    })
                                }
                            </select>
                        </div>
                        <div className="mb-2">
                            <label htmlFor="extensionVersion"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-code-branch" /></span>Extension Version</label>
                            <input
                                type="text"
                                className="form-control form-control-sm"
                                placeholder="Extension Version"
                                aria-label="Extension Version"
                                aria-describedby="basic-addon1"
                                value={extensionProperties.emConf.version}
                                onChange={(e) => {
                                    updateExtensionPropertiesHandler('emConf.version', e.target.value);
                                }}
                            />
                        </div>
                        <div className="mb-2">
                            <label htmlFor="extensionState"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-question" /></span>State</label>
                            <select
                                className="form-select"
                                aria-label="State"
                                onChange={(e) => {
                                    updateExtensionPropertiesHandler('emConf.state', e.target.value);
                                }}
                            >
                                <option>Please choose the state</option>
                                {
                                    stateOptions.map((state, index) => {
                                        return (
                                            <option key={index} value={state}>{state}</option>
                                        )
                                    })
                                }
                            </select>
                        </div>

                        <div className="form-check form-switch mb-2">
                            <label className="form-check-label" htmlFor="extensionDisableVersioning">
                                Disable versioning
                            </label>
                            <input
                                className="form-check-input"
                                type="checkbox"
                                role="switch"
                                id="extensionDisableVersioning"
                                checked={props.properties.emConf.disableVersioning}
                                onChange={(e) => {
                                    updateExtensionPropertiesHandler('emConf.disableVersioning', e.target.checked);
                                }}
                            />
                        </div>
                        <div className="form-check form-switch mb-2">
                            <label className="form-check-label" htmlFor="extensionDisableLocalization">Disable localization</label>
                            <input className="form-check-input" type="checkbox" role="switch" id="extensionDisableLocalization"
                                   checked={props.properties.emConf.disableLocalization}
                                   onChange={(e) => {
                                       updateExtensionPropertiesHandler('emConf.disableLocalization', e.target.checked);
                                   }}
                            />
                        </div>
                        <div className="form-check form-switch mb-2">
                            <label className="form-check-label" htmlFor="extensionGenerateDocumentation">Generate documentation</label>
                            <input className="form-check-input" type="checkbox" role="switch" id="extensionGenerateDocumentation"
                                      checked={props.properties.emConf.generateDocumentationTemplate}
                                   onChange={(e) => {
                                       updateExtensionPropertiesHandler('emConf.generateDocumentationTemplate', e.target.checked);
                                   }}
                            />
                        </div>
                        <div className="form-check form-switch mb-2">
                            <label className="form-check-label" htmlFor="extensionGenerateGitRepository">Generate git repository</label>
                            <input className="form-check-input" type="checkbox" role="switch" id="extensionGenerateGitRepository"
                                      checked={props.properties.emConf.generateEmptyGitRepository}
                                   onChange={(e) => {
                                       updateExtensionPropertiesHandler('emConf.generateEmptyGitRepository', e.target.checked);
                                   }}
                            />
                        </div>
                        <div className="form-check form-switch mb-2">
                            <label className="form-check-label" htmlFor="extensionGenerateEditorconfig">Generate editorconfig</label>
                            <input className="form-check-input" type="checkbox" role="switch" id="extensionGenerateEditorconfig"
                                        checked={props.properties.emConf.generateEditorConfig}
                                   onChange={(e) => {
                                       updateExtensionPropertiesHandler('emConf.generateEditorConfig', e.target.checked);
                                   }}
                            />
                        </div>

                        <div className="mb-2">
                            <label htmlFor="extensionSourceLanguageXliffFiles"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-question" /></span>Source language for xliff files</label>
                            <input
                                type="text"
                                className="form-control form-control-sm"
                                placeholder="Source language for xliff files"
                                aria-label="Source language for xliff files"
                                aria-describedby="basic-addon1"
                                value={extensionProperties.emConf.sourceLanguage}
                                onChange={(e) => {
                                    updateExtensionPropertiesHandler('emConf.sourceLanguage', e.target.value);
                                }}
                                disabled
                            />
                        </div>

                        <div className="mb-2">
                            <label htmlFor="extensionTargetTYPO3Versions"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-question" /></span>Target TYPO3 versions</label>
                            <select
                                className="form-select" aria-label="Default select example"
                                aria-label="Default select example"
                                onChange={handleTargetTYPO3VersionChange}
                            >
                                <option>Please choose the TYPO3 version </option>
                                {
                                    targetTYPO3Versions.map((targetTYPO3Version, index) => {
                                        return (
                                            <option key={index} value={targetTYPO3Version}>{targetTYPO3Version}</option>
                                        )
                                    })
                                }
                            </select>
                        </div>
                        <div className="mb-2">
                            <label
                                htmlFor="exampleFormControlTextarea1"
                                className="form-label">
                                <span className="me-2"><FontAwesomeIcon icon="fa-solid fa-question" /></span>
                                Depends on
                            </label>
                            <textarea
                                type="text"
                                className="form-control form-control-sm"
                                id="exampleFormControlTextarea1"
                                placeholder="typo3 => 12.4.0"
                                value={extensionProperties.emConf.dependsOn}
                                onChange={(e) => {
                                    updateExtensionPropertiesHandler('emConf.dependsOn', e.target.value);
                                }}
                                rows="5" />
                        </div>
                    </div>
                </div>
            </div>
        </Fragment>
    )
}
