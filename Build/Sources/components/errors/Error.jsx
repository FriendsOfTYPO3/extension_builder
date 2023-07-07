export const Error = (props) => {
	return (
		<>
            <div class="alert alert-warning alert-dismissible fade show" role="alert" id="eb-error-alert">
                <h4 class="alert-heading">{props.error.message}</h4>
                <p>{props.error.config.data}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </>
	)
}
