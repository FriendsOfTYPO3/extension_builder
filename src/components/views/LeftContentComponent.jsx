import {ExtensionPropertiesComponent} from "../accordions/ExtensionPropertiesComponent";
import {AuthorsListComponent} from "../accordions/AuthorsListComponent";
import {PluginsListComponent} from "../accordions/PluginsListComponent";
import {ModulesListComponent} from "../accordions/ModulesListComponent";

export const LeftContentComponent = (props) => {
    return (
        <>
            <div className="accordion" id="accordionExtensionProperties">
                <div className="accordion-item">
                    <h2 className="accordion-header" id="headingAccordionExtensionProperties">
                        <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseAccordionExtensionProperties" aria-expanded="true" aria-controls="collapseOne">
                            Extension Properties
                        </button>
                    </h2>
                    <div id="collapseAccordionExtensionProperties" className="accordion-collapse collapse"
                         aria-labelledby="headingAccordionExtensionProperties"
                         data-bs-parent="#accordionExtensionProperties">
                        <div className="accordion-body">
                            <ExtensionPropertiesComponent
                                extensionProperties={props.extensionProperties}
                                updateExtensionPropertiesHandler={props.updateExtensionPropertiesHandler}
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div className="accordion" id="accordionAuthors">
                <div className="accordion-item">
                    <h2 className="accordion-header" id="headingAccordionAuthors">
                        <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseAccordionAuthors" aria-expanded="true" aria-controls="collapseAccordionAuthors">
                            Extension authors
                        </button>
                    </h2>
                    <div id="collapseAccordionAuthors" className="accordion-collapse collapse"
                         aria-labelledby="headingAccordionAuthors"
                         data-bs-parent="#accordionAuthors">
                        <div className="accordion-body">
                            <AuthorsListComponent
                                authors={props.authors}
                                addAuthorsHandler={props.addNewAuthorHandler}
                                updateAuthorHandler={props.updateAuthorHandler}
                                removeAuthorHandler={props.removeAuthorHandler}
                                moveAuthor={props.moveAuthor}
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div className="accordion" id="accordionPlugins">
                <div className="accordion-item">
                    <h2 className="accordion-header" id="headingAccordionPlugins">
                        <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseAccordionPlugins" aria-expanded="true" aria-controls="collapseAccordionPlugins">
                            Frontend plugins
                        </button>
                    </h2>
                    <div id="collapseAccordionPlugins" className="accordion-collapse collapse"
                         aria-labelledby="headingAccordionPlugins"
                         data-bs-parent="#accordionPlugins">
                        <div className="accordion-body">
                            <PluginsListComponent
                                plugins={props.plugins}
                                addPluginsHandler={props.addNewPluginHandler}
                                updatePluginHandler={props.updatePluginHandler}
                                removePluginHandler={props.removePluginHandler}
                                movePlugin={props.movePlugin}
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div className="accordion" id="accordionModules">
                <div className="accordion-item">
                    <h2 className="accordion-header" id="headingAccordionModules">
                        <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseAccordionModules" aria-expanded="true" aria-controls="collapseAccordionModules">
                            Backend modules
                        </button>
                    </h2>
                    <div id="collapseAccordionModules" className="accordion-collapse collapse"
                         aria-labelledby="headingAccordionModules"
                         data-bs-parent="#accordionModules">
                        <div className="accordion-body">
                            <ModulesListComponent
                                modules={props.modules}
                                addModulesHandler={props.addNewModuleHandler}
                                updateModuleHandler={props.updateModuleHandler}
                                removeModuleHandler={props.removeModuleHandler}
                                moveModule={props.moveModule}
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div className="accordion" id="accordionDebug">
                <div className="accordion-item">
                    <h2 className="accordion-header" id="headingAccordionDebug">
                        <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseAccordionDebug" aria-expanded="true" aria-controls="collapseAccordionDebug">
                            Debug Output
                        </button>
                    </h2>
                    <div id="collapseAccordionDebug" className="accordion-collapse collapse"
                         aria-labelledby="headingAccordionDebug"
                         data-bs-parent="#accordionDebug">
                        <div className="accordion-body">
                            <h4>Extension Properties</h4>
                            <pre>
                                            {JSON.stringify(props.extensionProperties, null, 2)}
                                        </pre>
                            <hr />
                            <h4>Authors</h4>
                            <pre>
                                            {JSON.stringify(props.authors, null, 2)}
                                        </pre>
                            <hr />
                            <h4>Plugins</h4>
                            <pre>
                                            {JSON.stringify(props.plugins, null, 2)}
                                        </pre>
                            <hr />
                            <h4>Modules</h4>
                            <pre>
                                            {JSON.stringify(props.modules, null, 2)}
                                        </pre>
                        </div>
                    </div>
                </div>
            </div>

        </>

    )
}
