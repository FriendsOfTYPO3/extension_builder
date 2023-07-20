import { render, fireEvent } from "@testing-library/react";
import { SinglePluginComponent } from "./SinglePluginComponent";
import React from "react";

describe('SinglePluginComponent', () => {
    const mockPlugin = {
        id: '1',
        name: 'Plugin 1',
        key: 'plugin1',
        description: 'This is plugin 1',
        controllerActionsCachable: 'Blog => list, show',
        controllerActionsNonCachable: 'Blog => edit, update, delete',
    };

    const updatePluginMock = jest.fn();
    const removePluginMock = jest.fn();
    const movePluginMock = jest.fn();

    it('displays the correct initial plugin information', () => {
        const { getByLabelText } = render(<SinglePluginComponent plugin={mockPlugin} updatePluginHandler={updatePluginMock} removePluginHandler={removePluginMock} movePlugin={movePluginMock} index={0} plugins={[mockPlugin]} />);

        // expect(getByLabelText('Plugin Name')).toHaveValue(mockPlugin.name);
        // expect(getByLabelText('Plugin Key')).toHaveValue(mockPlugin.key);
        // expect(getByLabelText('Description')).toHaveValue(mockPlugin.description);
        // expect(getByLabelText('Cachable controller actions')).toHaveValue(mockPlugin.controllerActionsCachable);
        // expect(getByLabelText('Non cachable controller actions')).toHaveValue(mockPlugin.controllerActionsNonCachable);
    });

    it('calls updatePluginHandler when fields are changed', () => {
        const { getByLabelText } = render(<SinglePluginComponent plugin={mockPlugin} updatePluginHandler={updatePluginMock} removePluginHandler={removePluginMock} movePlugin={movePluginMock} index={0} plugins={[mockPlugin]} />);

        fireEvent.change(getByLabelText('Plugin Name'), { target: { value: 'New Plugin Name' } });
        expect(updatePluginMock).toHaveBeenCalledWith(mockPlugin.id, 'name', 'New Plugin Name');

        fireEvent.change(getByLabelText('Plugin Key'), { target: { value: 'newkey' } });
        expect(updatePluginMock).toHaveBeenCalledWith(mockPlugin.id, 'key', 'newkey');
    });

    /*it('calls removePluginHandler when the trash button is clicked', () => {
        const { getByText } = render(<SinglePluginComponent plugin={mockPlugin} updatePluginHandler={updatePluginMock} removePluginHandler={removePluginMock} movePlugin={movePluginMock} index={0} plugins={[mockPlugin]} />);

        fireEvent.click(getByText(/faTrash/i));
        expect(removePluginMock).toHaveBeenCalledWith(mockPlugin.id);
    });*/

   /* it('calls movePlugin when the up or down button is clicked', () => {
        const mockPlugin2 = {...mockPlugin, id: '2', name: 'Plugin 2'};
        const { getByText } = render(
            <SinglePluginComponent plugin={mockPlugin} updatePluginHandler={updatePluginMock} removePluginHandler={removePluginMock} movePlugin={movePluginMock} index={0} plugins={[mockPlugin, mockPlugin2]} />
        );

        fireEvent.click(getByText(/faArrowUp/i));
        expect(movePluginMock).toHaveBeenCalledWith(0, -1);

        fireEvent.click(getByText(/faArrowDown/i));
        expect(movePluginMock).toHaveBeenCalledWith(0, 1);
    });*/
});
