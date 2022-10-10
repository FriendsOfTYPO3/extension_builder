import {Fragment} from "react";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import {SinglePluginComponent} from "../SingleComponents/SinglePluginComponent";


export const PluginsListComponent = (props) => {
    return (
        <Fragment>
            <ul>
                {
                    props.plugins.map((plugin, index) => {
                        return (
                            <Fragment key={index}>
                                <SinglePluginComponent
                                    plugin={plugin}
                                    index={index}
                                    {...props}
                                />
                            </Fragment>
                        )
                    })
                }
            </ul>
            <button
                className="btn btn-success w-100"
                onClick={props.addPluginsHandler}>
                <FontAwesomeIcon className="me-1" icon="fa-solid fa-plus" />
                Add new plugin
            </button>
        </Fragment>
    )
}
