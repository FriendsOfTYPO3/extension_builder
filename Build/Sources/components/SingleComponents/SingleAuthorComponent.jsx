import { useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faArrowUp, faArrowDown, faTrash } from '@fortawesome/free-solid-svg-icons'
import React from "react";
import InputComponent from "../forms/input/InputComponent";
import SelectComponent from "../forms/select/SelectComponent";

export const SingleAuthorComponent = (props) => {

    const updateAuthorHandler = (field, value) => {
        props.updateAuthorHandler(props.index, field, value);
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
            <InputComponent
                label="Author"
                type="text"
                identifier="author"
                initialValue={props.author.name}
                validation={{ isRequired: true, minLength: 2 }}
                onChange={(value) => {
                    updateAuthorHandler('name', value);
                }}
            />
            <SelectComponent
                label="Role"
                initialValue={props.author.role}
                identifier="role"
                options={roles}
                defaultValue="Please choose the role"
                onChange={(value) => {
                    updateAuthorHandler('role', value);
                }}
            />
            <InputComponent
                label="E-Mail"
                type="email"
                identifier="email"
                initialValue={props.author.email}
                onChange={(value) => {
                    updateAuthorHandler('email', value);
                }}
            />
            <InputComponent
                label="Company"
                type="text"
                identifier="company"
                initialValue={props.author.company}
                onChange={(value) => {
                    updateAuthorHandler('company', value);
                }}
            />
            <div className="d-flex author-actions">
                <button
                    aria-label="Trash"
                    className="btn btn-danger me-auto"
                    onClick={() => {
                        props.removeAuthorHandler(props.author.id);
                    }}
                >
                   <FontAwesomeIcon icon={faTrash} />
                </button>
                <button
                    aria-label="Arrow Up"
                    className="btn btn-info me-1"
                    onClick={() => props.moveAuthor(props.index, -1)}
                    disabled={props.index === 0}
                >
                    <FontAwesomeIcon icon={faArrowUp} />
                </button>
                <button
                    aria-label="Arrow Down"
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
