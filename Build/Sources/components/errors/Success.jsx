export const Success = (props) => {
    return (
        <>
            <div className="alert alert-warning alert-dismissible fade show" role="alert" id="eb-success-alert">
                <h4 className="alert-heading">Successfull saved</h4>
                <div dangerouslySetInnerHTML={{ __html: props.success.data.success }} />
                <button type="button" className="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </>
    )
}
