import {ExtensionPropertiesAccordion} from "../accordions/ExtensionPropertiesAccordion";
import {AuthorsListAccordion} from "../accordions/AuthorsListAccordion";
import {PluginsListAccordion} from "../accordions/PluginsListAccordion";
import {ModulesListAccordion} from "../accordions/ModulesListAccordion";
import {DebugOutputAccordion} from "../accordions/DebugOutputAccordion";
import {Fragment, useContext} from "react";
import {ActionButtonsComponent} from "../ActionButtonsComponent";
import {EdgesContext} from "../../App";

export const SettingsPanelComponent = (props) => {
    return (
        <Fragment>
            <div>
                <ActionButtonsComponent
                    {...props}
                />
            </div>
            <div className="panel-group" id="accordion-left-panel">
                <ExtensionPropertiesAccordion
                    properties={props.properties}
                    updateExtensionPropertiesHandler={props.updateExtensionPropertiesHandler}
                />
                <AuthorsListAccordion
                    authors={props.authors}
                    addAuthorsHandler={props.addNewAuthorHandler}
                    updateAuthorHandler={props.updateAuthorHandler}
                    removeAuthorHandler={props.removeAuthorHandler}
                    moveAuthor={props.moveAuthor}
                />
                <PluginsListAccordion
                    plugins={props.plugins}
                    addPluginsHandler={props.addNewPluginHandler}
                    updatePluginHandler={props.updatePluginHandler}
                    removePluginHandler={props.removePluginHandler}
                    movePlugin={props.movePlugin}
                />
                <ModulesListAccordion
                    modules={props.modules}
                    addModulesHandler={props.addNewModuleHandler}
                    updateModuleHandler={props.updateModuleHandler}
                    removeModuleHandler={props.removeModuleHandler}
                    moveModule={props.moveModule}
                />
            </div>
            {/*<div className="panel-group" id="accordion-left-panel-debug">
                <DebugOutputAccordion
                    properties={props.properties}
                    authors={props.authors}
                    plugins={props.plugins}
                    modules={props.modules}
                />
            </div>*/}
        </Fragment>
    )
}
