function convertModulesToNodes(modules) {
    // needed for the node
    /*
    {
        id,
        type,
        position: {
            x,
            y
        },
        data: {
            label,
            objectType,
            isAggregateRoot,
            enableSorting,
            addDeletedField,
            addHiddenField,
            addStarttimeEndtimeFields,
            enableCategorization,
            description,
            mapToExistingTable,
            extendExistingModelClass,
            actions: {
                actionIndex,
                actionList,
                actionShow,
                actionNewCreate,
                actionEditUpdate,
                actionDelete
            },
            customActions -> Array,
            properties -> Array,
            relations -> Array
        },
        positionAbsolute: {
            x,
            y
        }
    }
     */

    for (let i = 0; i < modules.length; i++) {
        console.log(`Element ${i}:`, modules[i]);
        // Hier kannst du auf einzelne Eigenschaften des aktuellen Objekts zugreifen, z.B.:
        console.log(`Name: ${modules[i].name}`);
        // Und weitere Verarbeitungen für jedes Objekt im Array durchführen...
    }

    console.log('convertModuleToNodes');
    console.log(modules);

    let result = modules.map((item, index) => ({
        id: `dndnode_${index}`,
        uid: item.value.objectsettings.uid,
        type: "customModel",
        position: {
            x: item.config.position[0],
            y: item.config.position[1]
        },
        data: {
            label: item.name,
            objectType: "foobar",
            isAggregateRoot: item.value.objectsettings.aggregateRoot,
            enableSorting: item.value.objectsettings.sorting,
            addDeletedField: item.value.objectsettings.addDeletedField,
            addHiddenField: item.value.objectsettings.addHiddenField,
            addStarttimeEndtimeFields: item.value.objectsettings.addStarttimeEndtimeFields,
            enableCategorization: item.value.objectsettings.categorizable,
            description: item.value.objectsettings.description,
            mapToExistingTable: item.value.objectsettings.mapToTable,
            extendExistingModelClass: item.value.objectsettings.parentClass,
            actions: {
                actionIndex: item.value.actionGroup._default0_index,
                actionList: item.value.actionGroup._default1_list,
                actionShow: item.value.actionGroup._default2_show,
                actionNewCreate: item.value.actionGroup._default3_new_create,
                actionEditUpdate: item.value.actionGroup._default4_edit_update,
                actionDelete: item.value.actionGroup._default5_delete
            },
            customActions: item.value.actionGroup.customActions,
            properties: item.value.propertyGroup.properties,
            relations: item.value.relationGroup.relations
        },
        dragHandle: ".drag-handle",
        draggable: true,
        width: 300,
        height: 257,
        selected: false,
        positionAbsolute: {
            x: item.config.position[0],
            y: item.config.position[1]
        },
        dragging: false
    }));

    console.log('result');
    console.log(result);

    return result;
}

export default convertModulesToNodes;

/*
{
    "id": "dndnode_0",
    "type": "customModel",
    "position": {
        "x": 550,
        "y": 131
    },
    "data": {
        "label": "Testmodule",
        "objectType": "",
        "isAggregateRoot": true,
        "enableSorting": false,
        "addDeletedField": true,
        "addHiddenField": true,
        "addStarttimeEndtimeFields": true,
        "enableCategorization": false,
        "description": "",
        "mapToExistingTable": "",
        "extendExistingModelClass": "",
        "actions": {
            "actionIndex": true,
            "actionList": true,
            "actionShow": false,
            "actionNewCreate": false,
            "actionEditUpdate": false,
            "actionDelete": false
        },
        "customActions": [],
        "properties": [],
        "relations": []
    },
    "dragHandle": ".drag-handle",
    "draggable": true,
    "width": 300,
    "height": 257,
    "selected": true,
    "positionAbsolute": {
        "x": 550,
        "y": 131
    },
    "dragging": false
}
 */
