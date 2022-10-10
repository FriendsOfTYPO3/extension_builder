import './App.scss';
import {useEffect, useState} from "react";
import {LeftContentComponent} from "./components/views/LeftContentComponent";
import {RightContentComponent} from "./components/views/RightContentComponent";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

const initialNodes = [];
const initialEdges = [];

function App() {
    // Zustand für das Ein- und Ausklappen der linken Spalte
    const [isLeftColumnVisible, setLeftColumnVisible] = useState(true);

    // Funktion zum Umschalten der Sichtbarkeit der linken Spalte
    const toggleLeftColumn = () => {
        setLeftColumnVisible(!isLeftColumnVisible);
    }

    const [extensionProperties, setExtensionProperties] = useState(
        {
            extensionName: '',
            extensionVendorName: '',
            extensionKey: '',
            extensionDescription: '',
            extensionCategory: '',
            extensionVersion: '',
            extensionState: '',
            extensionSourceLanguageXliffFiles: 'en',
            extensionTargetTYPO3Versions: '12.4',
            extensionDependsOn: '',
            extensionDisableVersioning: false,
            extensionDisableLocalization: false,
            extensionGenerateDocumentation: false,
            extensionGenerateGitRepository: false,
            extensionGenerateEditorconfig: false,
        }
    );
    const [authors, setAuthors] = useState([]);
    const [plugins, setPlugins] = useState([]);
    const [modules, setModules] = useState([]);

    const defaultAuthor = {
        name: '',
        role: '',
        email: '',
        company: '',
    }

    const defaultModule = {
        name: '',
        key: '',
        description: '',
        tabLabel: '',
        mainModule: '',
        controllerActionsCachable: ''
    }

    const defaultPlugin = {
        name: '',
        key: '',
        description: '',
        controllerActionsCachable: '',
        controllerActionsNonCachable: '',
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

    const updateExtensionPropertiesHandler = (
        extensionName,
        extensionVendorName,
        extensionKey,
        extensionDescription,
        extensionCategory,
        extensionVersion,
        extensionState,
        extensionSourceLanguageXliffFiles,
        extensionTargetTYPO3Versions,
        extensionDependsOn,
        extensionDisableVersioning,
        extensionDisableLocalization,
        extensionGenerateDocumentation,
        extensionGenerateGitRepository,
        extensionGenerateEditorconfig
    ) => {
       setExtensionProperties({
            extensionName: extensionName,
            extensionVendorName: extensionVendorName,
            extensionKey: extensionKey,
            extensionDescription: extensionDescription,
            extensionCategory: extensionCategory,
            extensionVersion: extensionVersion,
            extensionState: extensionState,
            extensionSourceLanguageXliffFiles: extensionSourceLanguageXliffFiles,
            extensionTargetTYPO3Versions: extensionTargetTYPO3Versions,
            extensionDependsOn: extensionDependsOn,
            extensionDisableVersioning: extensionDisableVersioning,
            extensionDisableLocalization: extensionDisableLocalization,
            extensionGenerateDocumentation: extensionGenerateDocumentation,
            extensionGenerateGitRepository: extensionGenerateGitRepository,
            extensionGenerateEditorconfig: extensionGenerateEditorconfig,
       })
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
                    return {...plugin, [field]: value};
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
                    return {...module, [field]: value};
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
                            extensionProperties={extensionProperties}
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
                        />
                    </div>
                </div>
                <div style={{left: isLeftColumnVisible ? '400px' : '0', width: isLeftColumnVisible ? 'calc(100vw - 400px)' : '100vw'}} id="right-column" className="no-padding full-height">
                    <div >
                        <RightContentComponent />
                    </div>
                </div>
            </div>
        </div>
    );
}

export default App;
