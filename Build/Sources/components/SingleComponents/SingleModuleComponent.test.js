import { render, fireEvent } from "@testing-library/react";
import { SingleModuleComponent } from "./SingleModuleComponent";
import React from "react";

describe('SingleModuleComponent', () => {
    const mockModule = {
        id: '1',
        name: 'Module 1',
        key: 'module1',
        description: 'This is module 1',
        tabLabel: 'Tab 1',
        mainModule: 'web',
        controllerActionsCachable: 'Blog => edit, update, delete',
    };

    const updateModuleMock = jest.fn();
    const removeModuleMock = jest.fn();
    const moveModuleMock = jest.fn();

    it('displays the correct initial module information', () => {
        const { getByLabelText } = render(<SingleModuleComponent module={mockModule} updateModuleHandler={updateModuleMock} removeModuleHandler={removeModuleMock} moveModule={moveModuleMock} index={0} modules={[mockModule]} />);

        // expect(getByLabelText('Name')).toHaveValue(mockModule.name);
        // expect(getByLabelText('Key')).toHaveValue(mockModule.key);
        // expect(getByLabelText('Description')).toHaveValue(mockModule.description);
        // expect(getByLabelText('Label')).toHaveValue(mockModule.tabLabel);
        // expect(getByLabelText('Main module')).toHaveValue(mockModule.mainModule);
        // expect(getByLabelText('Cachable controller actions')).toHaveValue(mockModule.controllerActionsCachable);
    });

    it('calls updateModuleHandler when fields are changed', () => {
        const { getByLabelText } = render(<SingleModuleComponent module={mockModule} updateModuleHandler={updateModuleMock} removeModuleHandler={removeModuleMock} moveModule={moveModuleMock} index={0} modules={[mockModule]} />);

        fireEvent.change(getByLabelText('Name'), { target: { value: 'New Module Name' } });
        expect(updateModuleMock).toHaveBeenCalledWith(mockModule.id, 'name', 'New Module Name');

        fireEvent.change(getByLabelText('Key'), { target: { value: 'newkey' } });
        expect(updateModuleMock).toHaveBeenCalledWith(mockModule.id, 'key', 'newkey');
    });

    it('calls removeModuleHandler when the trash button is clicked', () => {
        const { getByLabelText } = render(<SingleModuleComponent module={mockModule} updateModuleHandler={updateModuleMock} removeModuleHandler={removeModuleMock} moveModule={moveModuleMock} index={0} modules={[mockModule]} />);

        fireEvent.click(getByLabelText('Trash'));
        expect(removeModuleMock).toHaveBeenCalledWith(mockModule.id);
    });

    /*it('calls moveModule when the up or down button is clicked', () => {
        const mockModule2 = {...mockModule, id: '2', name: 'Module 2'};
        const { getByLabelText } = render(
            <SingleModuleComponent module={mockModule} updateModuleHandler={updateModuleMock} removeModuleHandler={removeModuleMock} moveModule={moveModuleMock} index={0} modules={[mockModule, mockModule2]} />
        );

        fireEvent.click(getByLabelText('ArrowUp'));
        expect(moveModuleMock).toHaveBeenCalledWith(0, -1);

        fireEvent.click(getByLabelText('ArrowDown'));
        expect(moveModuleMock).toHaveBeenCalledWith(0, 1);
    });*/
});
