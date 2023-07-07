import './App.scss';
import {useEffect, useState} from "react";
import {LeftContentComponent} from "./components/views/LeftContentComponent";
import {RightContentComponent} from "./components/views/RightContentComponent";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {getDemoExtensionKey} from "./helper";

const initialNodes = [];
const initialEdges = [];

function App() {
    // Zustand für das Ein- und Ausklappen der linken Spalte
    const [isLeftColumnVisible, setLeftColumnVisible] = useState(true);

    // Funktion zum Umschalten der Sichtbarkeit der linken Spalte
    const toggleLeftColumn = () => {
        setLeftColumnVisible(!isLeftColumnVisible);
    }

    const [properties, setProperties] = useState(
        {
            backendModules: [],
            description: "",
            emConf: {
                "category": "",
                "custom_category": "",
                "dependsOn": "",
                "disableLocalization": false,
                "disableVersioning": false,
                "generateDocumentationTemplate": true,
                "generateEditorConfig": true,
                "generateEmptyGitRepository": true,
                "sourceLanguage": "en",
                "state": "alpha",
                "targetVersion": "12.4",
                "version": "1.0.0"
            },
            extensionKey: "",
            name: "",
            originalExtensionKey: "",
            originalVendorName: "",
            persons: [],
            plugins: [],
            vendorName: ""
        }
    );
    const [authors, setAuthors] = useState([]);
    const [plugins, setPlugins] = useState([]);
    const [modules, setModules] = useState([]);

    const defaultAuthor = {
        company: '',
        email: '',
        name: '',
        role: '',
    }

    const defaultModule = {
        actions : {
            controllerActionCombinations: ""
        },
        description: '',
        key: '',
        mainModule: '',
        name: '',
        tabLabel: '',
    }

    const defaultPlugin = {
        actions: {
            controllerActionCombinations: "",
            noncacheableActions: ""
        },
        description: '',
        key: '',
        name: '',
    }

    const addNewAuthorHandler = () => {
        setAuthors((prevAuthors) => {
            return [...prevAuthors, {...defaultAuthor, id: Math.random().toString()}];
        });
    }

    const addNewModuleHandler = () => {
        setModules((prevModules) => {
            return [...prevModules, {...defaultModule, id: Math.random().toString()}];
        });
    }

    const addNewPluginHandler = () => {
        setPlugins((prevPlugins) => {
            return [...prevPlugins, {...defaultPlugin, id: Math.random().toString()}];
        });
    }

    const updateExtensionPropertiesHandler = (key, value) => {
        if (key.includes('.')) {
            const [parentKey, childKey] = key.split('.');
            setProperties(prevProperties => ({
                ...prevProperties,
                [parentKey]: {
                    ...prevProperties[parentKey],
                    [childKey]: value,
                },
            }));
        } else {
            setProperties(prevProperties => ({
                ...prevProperties,
                [key]: value,
            }));
        }
    }


    const updateAuthorHandler = (authorId, field, value) => {
        setAuthors((prevAuthors) => {
            return prevAuthors.map((author) => {
                if (author.id === authorId) {
                    return {...author, [field]: value};
                } else {
                    return author;
                }
            });
        });
    };

const updatePluginHandler = (pluginId, field, value) => {
    setPlugins((prevPlugins) => {
        return prevPlugins.map((plugin) => {
            if (plugin.id === pluginId) {
                if (field.includes('.')) {
                    const [parentKey, childKey] = field.split('.');
                    return {...plugin, [parentKey]: {...plugin[parentKey], [childKey]: value}};
                } else {
                    return {...plugin, [field]: value};
                }
            } else {
                return plugin;
            }
        });
    });
}

const updateModuleHandler = (moduleId, field, value) => {
    setModules((prevModules) => {
        return prevModules.map((module) => {
            if (module.id === moduleId) {
                if (field.includes('.')) {
                    const [parentKey, childKey] = field.split('.');
                    return {...module, [parentKey]: {...module[parentKey], [childKey]: value}};
                } else {
                    return {...module, [field]: value};
                }
            } else {
                return module;
            }
        });
    });
}

    const removeAuthorHandler = (authorId) => {
        // TODO Testen !!!
        setAuthors((prevAuthors) => {
            return prevAuthors.filter((author) => author.id !== authorId);
        });
    }

    const removePluginHandler = (pluginId) => {
        // TODO Testen !!!
        setPlugins((prevPlugins) => {
            return prevPlugins.filter((plugin) => plugin.id !== pluginId);
        });
    }

    const removeModuleHandler = (moduleId) => {
        // TODO Testen !!!
        setModules((prevModules) => {
            return prevModules.filter((module) => module.id !== moduleId);
        });
    }

    const moveAuthor = (index, direction) => {
        setAuthors(prevAuthors => {
            const newAuthors = [...prevAuthors];
            const targetIndex = index + direction;

            // Überprüfen, ob der Zielindex innerhalb der gültigen Bereichsgrenzen liegt
            if (targetIndex >= 0 && targetIndex < newAuthors.length) {
                // Elemente tauschen
                const temp = newAuthors[targetIndex];
                newAuthors[targetIndex] = newAuthors[index];
                newAuthors[index] = temp;
            }

            return newAuthors;
        });
    }


    const movePlugin = (index, direction) => {
        setPlugins(prevPlugins => {
            const newPlugins = [...prevPlugins];
            const targetIndex = index + direction;

            // Überprüfen, ob der Zielindex innerhalb der gültigen Bereichsgrenzen liegt
            if (targetIndex >= 0 && targetIndex < newPlugins.length) {
                // Elemente tauschen
                const temp = newPlugins[targetIndex];
                newPlugins[targetIndex] = newPlugins[index];
                newPlugins[index] = temp;
            }

            return newPlugins;
        });
    }

    const moveModule = (index, direction) => {
        setModules(prevModules => {
            const newModules = [...prevModules];
            const targetIndex = index + direction;

            // Überprüfen, ob der Zielindex innerhalb der gültigen Bereichsgrenzen liegt
            if (targetIndex >= 0 && targetIndex < newModules.length) {
                // Elemente tauschen
                const temp = newModules[targetIndex];
                newModules[targetIndex] = newModules[index];
                newModules[index] = temp;
            }

            return newModules;
        });
    }

    useEffect(() => {
        const leftColumn = document.getElementById('left-column');
        if (leftColumn) {
            leftColumn.style.opacity = isLeftColumnVisible ? '1' : '0';
        }
    }, [isLeftColumnVisible]);

    const handleDemoInput = () => {
        updateExtensionPropertiesHandler('description', 'This is a demo extension');
        updateExtensionPropertiesHandler('emConf.category', 'custom');
        updateExtensionPropertiesHandler('emConf.dependsOn', 'TYPO3 12');
        updateExtensionPropertiesHandler('emConf.disableLocalization', false);
        updateExtensionPropertiesHandler('emConf.disableVersioning', true);
        updateExtensionPropertiesHandler('emConf.generateDocumentationTemplate', true);
        updateExtensionPropertiesHandler('emConf.generateEditorConfig', true);
        updateExtensionPropertiesHandler('emConf.generateEmptyGitRepository', true);
        updateExtensionPropertiesHandler('emConf.sourceLanguage', 'en');
        updateExtensionPropertiesHandler('emConf.state', 'beta');
        updateExtensionPropertiesHandler('emConf.targetVersion', '1.0.0');
        updateExtensionPropertiesHandler('emConf.version', '1.0.0');
        updateExtensionPropertiesHandler('extensionKey', getDemoExtensionKey());
        updateExtensionPropertiesHandler('name', 'Demo Extension');
        updateExtensionPropertiesHandler('vendorName', 'Treupo');
    }

    return (
        <div className="App container-fluid">
            <button
                id="btn-sidebar-collapse"
                type="button"
                className={`btn btn-primary position-fixed ${isLeftColumnVisible ? 'expanded' : 'collapsed'}`}
                onClick={toggleLeftColumn}
            >
                {isLeftColumnVisible && <FontAwesomeIcon className="p-0 m-0" icon="fa-solid fa-arrow-left" />}
                {!isLeftColumnVisible && <FontAwesomeIcon className="p-0 m-0" icon="fa-solid fa-arrow-right" />}
            </button>
            <div className="collapse" id="collapseExample">
                <div className="card card-body">
                    Some placeholder content for the collapse component. This panel is hidden by default but revealed
                    when the user activates the relevant trigger.
                </div>
            </div>
            <div className="row">
                <div id="left-column" className="no-padding full-height">
                    <div className="p-1">
                        <LeftContentComponent
                            properties={properties}
                            authors={authors}
                            plugins={plugins}
                            modules={modules}
                            addNewAuthorHandler={addNewAuthorHandler}
                            addNewModuleHandler={addNewModuleHandler}
                            addNewPluginHandler={addNewPluginHandler}
                            updateExtensionPropertiesHandler={updateExtensionPropertiesHandler}
                            updateAuthorHandler={updateAuthorHandler}
                            updateModuleHandler={updateModuleHandler}
                            updatePluginHandler={updatePluginHandler}
                            removeAuthorHandler={removeAuthorHandler}
                            removePluginHandler={removePluginHandler}
                            removeModuleHandler={removeModuleHandler}
                            moveAuthor={moveAuthor}
                            movePlugin={movePlugin}
                            moveModule={moveModule}
                            handleDemoInput={handleDemoInput}
                        />
                    </div>
                </div>
               {/* <div style={{left: isLeftColumnVisible ? '400px' : '0', width: isLeftColumnVisible ? 'calc(100vw - 400px)' : '100vw'}} id="right-column" className="no-padding full-height">
                    <div >
                        <RightContentComponent />
                    </div>
                </div>*/}
            </div>
        </div>
    );
}

export default App;
