import './App.scss';
import {useNodesState, useEdgesState} from 'reactflow';
import {useEffect, useState, useContext, createContext} from "react";
import {LeftContentComponent} from "./components/views/LeftContentComponent";
import {RightContentComponent} from "./components/views/RightContentComponent";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import initialProperties from "./initialValues/properties";
import defaultAuthor from "./initialValues/author";
import defaultModule from "./initialValues/module";
import defaultPlugin from "./initialValues/plugin";

export const NodesContext = createContext([]);
export const EdgesContext = createContext([]);
export const CustomModelNodeIndexContext = createContext(0);

function App() {
    // Nodes for ReactFlow
    const [nodes, setNodes, onNodesChange] = useNodesState([]);
    const [edges, setEdges, onEdgesChange] = useEdgesState([]);
    const [customModelNodeIndex, setCustomModelNodeIndex] = useState(0);

    const [properties, setProperties] = useState(initialProperties);
    const [authors, setAuthors] = useState([]);
    const [plugins, setPlugins] = useState([]);
    const [modules, setModules] = useState([]);

    // Zustand für das Ein- und Ausklappen der linken Spalte
    const [isLeftColumnVisible, setLeftColumnVisible] = useState(true);

    const handleNodesChanged = (newNodes) => {
        setNodes(newNodes);
    };

    const handleEdgesChanged = (newEdges) => {
        setEdges(newEdges);
    };

    useEffect(() => {
        const leftColumn = document.getElementById('left-column');
        if (leftColumn) {
            leftColumn.style.opacity = isLeftColumnVisible ? '1' : '0';
        }
    }, [isLeftColumnVisible]);

    // Funktion zum Umschalten der Sichtbarkeit der linken Spalte
    const toggleLeftColumn = () => {
        setLeftColumnVisible(!isLeftColumnVisible);
    }

    const addNewItemHandler = (setter, defaultItem) => () => {
        setter((prevItems) => {
            return [...prevItems, {...defaultItem }];
        });
    }

    const addNewAuthorHandler = addNewItemHandler(setAuthors, defaultAuthor);
    const addNewModuleHandler = addNewItemHandler(setModules, defaultModule);
    const addNewPluginHandler = addNewItemHandler(setPlugins, defaultPlugin);

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
        setAuthors((prevAuthors) => {
            return prevAuthors.filter((author) => author.id !== authorId);
        });
    }

    const removePluginHandler = (pluginId) => {
        setPlugins((prevPlugins) => {
            return prevPlugins.filter((plugin) => plugin.id !== pluginId);
        });
    }

    const removeModuleHandler = (moduleId) => {
        setModules((prevModules) => {
            return prevModules.filter((module) => module.id !== moduleId);
        });
    }

    const moveElement = (index, direction, array, setArray) => {
        setArray(prevArray => {
            const newArray = [...prevArray];
            const targetIndex = index + direction;

            if (targetIndex >= 0 && targetIndex < newArray.length) {
                const temp = newArray[targetIndex];
                newArray[targetIndex] = newArray[index];
                newArray[index] = temp;
            }

            return newArray;
        });
    }

    const moveAuthor = (index, direction) => moveElement(index, direction, authors, setAuthors);
    const movePlugin = (index, direction) => moveElement(index, direction, plugins, setPlugins);
    const moveModule = (index, direction) => moveElement(index, direction, modules, setModules);

    const handleOpenExtension = (extension) => {
        const working = JSON.parse(extension.working);

        // Sets properties.
        setProperties(prev => ({...prev, ...working.properties}));

        // Updated authors, plugins and modules with new IDs.
        setAuthors(working.properties.persons);
        setPlugins(working.properties.plugins);
        setModules(working.properties.backendModules);

        // Check if nodes or edges are available, and update them.
        setNodes(working.nodes ? working.nodes: []);
        setEdges(working.edges ? working.edges : []);

        // Set the custom model node index depending on the amount of nodes.
        setCustomModelNodeIndex(working.nodes ? working.nodes.length : 0);
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
                        <EdgesContext.Provider value={{edges, setEdges, onEdgesChange}}>
                            <NodesContext.Provider value={{nodes, setNodes, onNodesChange}}>
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
                                    handleOpenExtension={handleOpenExtension}
                                />
                            </NodesContext.Provider>
                        </EdgesContext.Provider>
                    </div>
                </div>
               <div style={{left: isLeftColumnVisible ? '400px' : '0', width: isLeftColumnVisible ? 'calc(100vw - 400px)' : '100vw'}} id="right-column" className="no-padding full-height">
                    <div >
                        <CustomModelNodeIndexContext.Provider value={{customModelNodeIndex, setCustomModelNodeIndex}}>
                            <EdgesContext.Provider value={{edges, setEdges, onEdgesChange}}>
                                <NodesContext.Provider value={{nodes, setNodes, onNodesChange}}>
                                    <RightContentComponent />
                                </NodesContext.Provider>
                            </EdgesContext.Provider>
                        </CustomModelNodeIndexContext.Provider>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default App;
