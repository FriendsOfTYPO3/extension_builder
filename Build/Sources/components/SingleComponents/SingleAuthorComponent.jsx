import { useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faArrowUp, faArrowDown, faTrash } from '@fortawesome/free-solid-svg-icons'

export const SingleAuthorComponent = (props) => {

    const updateAuthorHandler = (field, value) => {
        props.updateAuthorHandler(props.author.id, field, value);
    };

    const roles = [
        "Developer",
        "Project Manager",
        "Designer",
        "Tester",
        "Documentation Writer",
        "Reviewer",
        "Support",
        "Translator",
        "Security",
    ];

    return (
        <div className="mb-5">
            <div className="mb-3 input-group">
                {/*<label htmlFor="inputGroupSelect01"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-user" /></span>Author</label>*/}
                <span className="input-group-text" id="basic-addon1">Author</span>
                <input
                    type="text"
                    className="form-control"
                    placeholder="Author Name"
                    aria-label="Author Name"
                    aria-describedby="basic-addon1"
                    value={props.author.name}
                    onChange={(e) => {
                        updateAuthorHandler('name', e.target.value);
                    }}
                />
            </div>
            <div className="mb-3 input-group">
                {/*<label htmlFor="role"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-user-tag" /></span>Role</label>*/}
                <span className="input-group-text" id="basic-addon1">Role</span>
                <select
                    className="form-select"
                    aria-label="Role"
                    id="role"
                    onChange={(e) => {
                        updateAuthorHandler('role', e.target.value);
                    }}
                >
                    <option>Please choose the role</option>
                    {
                        roles.map((role, index) => {
                            return (
                                <option key={index} value={role}>{role}</option>
                            )
                        })
                    }
                </select>
            </div>
            <div className="mb-3 input-group">
                {/*<label htmlFor="inputGroupSelect01"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-envelope" /></span>E-Mail</label>*/}
                <span className="input-group-text" id="basic-addon1">E-Mail</span>
                <input
                    type="text"
                    id="email"
                    className="form-control"
                    placeholder="Author E-Mail"
                    aria-label="Author E-Mail"
                    aria-describedby="basic-addon1"
                    value={props.author.email}
                    onChange={(e) => {
                        updateAuthorHandler('email', e.target.value);
                    }}
                />
            </div>
            <div className="mb-3 input-group">
                {/*<label htmlFor="inputGroupSelect01"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-building" /></span>Company</label>*/}
                <span className="input-group-text" id="basic-addon1">Company</span>
                <input
                    type="text"
                    id="company"
                    className="form-control"
                    placeholder="Company"
                    aria-label="Company"
                    aria-describedby="basic-addon1"
                    value={props.author.company}
                    onChange={(e) => {
                        updateAuthorHandler('company', e.target.value);
                    }}
                />
            </div>
            <div className="d-flex author-actions">
                <button
                    className="btn btn-danger me-auto"
                    onClick={() => {
                        props.removeAuthorHandler(props.author.id);
                    }}
                >
                   <FontAwesomeIcon icon={faTrash} />
                </button>
                <button
                    className="btn btn-info me-1"
                    onClick={() => props.moveAuthor(props.index, -1)}
                    disabled={props.index === 0}
                >
                    <FontAwesomeIcon icon={faArrowUp} />
                </button>
                <button
                    className="btn btn-info"
                    onClick={() => props.moveAuthor(props.index, 1)}
                    disabled={props.index === props.authors.length - 1}
                >

                    <FontAwesomeIcon icon={faArrowDown} />
                </button>
            </div>
        </div>
    );
};
