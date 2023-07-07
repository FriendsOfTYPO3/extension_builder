export const TYPO3StyledAccordionGroup = (props) => {
	return (
        <div className="panel-group" id={props.id}>
            {props.children}
        </div>
	)
}
