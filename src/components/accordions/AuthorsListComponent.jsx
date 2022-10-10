import {Fragment} from "react";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import {SingleAuthorComponent} from "../SingleComponents/SingleAuthorComponent";


export const AuthorsListComponent = (props) => {
    return (
        <Fragment>
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
            </ul>
            <button
                className="btn btn-success w-100"
                onClick={props.addAuthorsHandler}>
                <FontAwesomeIcon className="me-1" icon="fa-solid fa-user-plus" />
                Add new author
            </button>
        </Fragment>
    )
}
