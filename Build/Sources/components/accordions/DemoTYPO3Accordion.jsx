export const DemoTYPO3Accordion = () => {
	return (
		<>
            <div class="panel-group" id="accordionExampleAdvanced">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title" id="heading-panel1">
                            <a href="#" data-bs-toggle="collapse" data-bs-target="#panel1" aria-expanded="true" aria-controls="panel1">
                                <span class="caret"></span>
                                <strong>Extension authors</strong>
                            </a>
                        </h3>
                    </div>
                    <div id="panel1" class="accordion-collapse collapse show" aria-labelledby="heading-panel1"
                         data-bs-parent="#accordion-left-panel">
                        <div class="panel-body">
                            Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut
                            labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores
                        </div>
                    </div>
                </div>
            </div>
        </>
	)
}
