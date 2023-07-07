import {Fragment} from "react";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import {SingleAuthorComponent} from "../SingleComponents/SingleAuthorComponent";


export const AuthorsListAccordion = (props) => {
    return (
        <Fragment>
            <div className="panel panel-default">
                <div className="panel-heading">
                    <h3 className="panel-title" id="heading-panel-authors">
                        <a href="#" className="collapsed" data-bs-toggle="collapse" data-bs-target="#panel-authors" aria-expanded="true" aria-controls="panel-authors">
                            <span className="caret"></span>
                            <strong>Extension authors</strong>
                        </a>
                    </h3>
                </div>
                <div id="panel-authors" className="accordion-collapse collapse" aria-labelledby="heading-panel-authors"
                     data-bs-parent="#accordion-left-panel">
                    <div className="panel-body">
                    <span className="d-block mb-2">These authors will be added to the composer.json and ext_emconf.php files</span>
                        <ul>
                            {
                                props.authors.map((author, index) => {
                                    return (
                                        <Fragment key={author.id}>
                                            <SingleAuthorComponent
                                                author={author}
                                                index={index}
                                                {...props}
                                            />
                                        </Fragment>
                                    )
                                })
                            }
                            {props.authors.length <= 0 ?? <li>No authors yet</li>}
                        </ul>
                        <button
                            className="btn btn-success w-100"
                            onClick={props.addAuthorsHandler}>
                            <FontAwesomeIcon className="me-1" icon="fa-solid fa-user-plus" />
                            Add new author
                        </button>
                    </div>
                </div>
            </div>
        </Fragment>
    )
}
