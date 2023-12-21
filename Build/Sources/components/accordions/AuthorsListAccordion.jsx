import { memo } from "react";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { SingleAuthorComponent } from "../SingleComponents/SingleAuthorComponent";

export const AuthorsListAccordion = memo((props) => {
    return (
        <div className="panel panel-default">
            <div className="panel-heading">
                <h3 className="panel-title fs-3" id="heading-panel-authors">
                    <a href="#" className="collapsed" data-bs-toggle="collapse" data-bs-target="#panel-authors" aria-expanded="true" aria-controls="panel-authors">
                        <span className="caret"></span>
                        <strong>Extension authors</strong>
                    </a>
                </h3>
            </div>
            <div id="panel-authors" className="accordion-collapse collapse" aria-labelledby="heading-panel-authors"
                 data-bs-parent="#accordion-left-panel">
                <div className="panel-body py-2">
                    <span className="d-block mb-2">These authors will be added to the composer.json and ext_emconf.php files</span>
                    <ul>
                        {
                            props.authors.map((author, index) => (
                                <SingleAuthorComponent
                                    key={author.id}
                                    author={author}
                                    index={index}
                                    {...props}
                                />
                            ))
                        }
                    </ul>
                    {props.authors.length === 0 && <div className="alert alert-danger" role="alert">No authors yet</div>}
                    <button
                        className="fs-3 btn btn-success w-100"
                        onClick={props.addAuthorsHandler}>
                        <FontAwesomeIcon className="me-1" icon={['fas', 'user-plus']} />
                        Add new author
                    </button>
                </div>
            </div>
        </div>
    )
})
