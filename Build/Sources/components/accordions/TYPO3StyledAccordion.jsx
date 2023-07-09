import PropTypes from "prop-types";

export const TYPO3StyledAccordion = (props) => {
	return (
        <div className="panel panel-default">
            <div className="panel-heading">
                <h3 className="panel-title" id={`simple-heading-panel${props.id}`}>
                    <a href="#" className="collapsed" data-bs-toggle="collapse" data-bs-target={`#simple-panel${props.id}`} aria-expanded="false" aria-controls={`simple-panel${props.id}`}>
                        <span className="caret"></span>
                        <strong>{props.title}</strong>
                    </a>
                </h3>
            </div>
            <div id={`simple-panel${props.id}`} className="collapse" aria-labelledby={`simple-heading-panel${props.id}`}>
                <div className="panel-body">
                    {props.children}
                </div>
            </div>
        </div>
	)
}

TYPO3StyledAccordion.propTypes = {
    title: PropTypes.string.isRequired,
    id: PropTypes.string.isRequired,
    parentId: PropTypes.string.isRequired
}
