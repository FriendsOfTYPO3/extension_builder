// from
// "wires": [
//     {
//         "src": {
//             "moduleId": 0, ➡️ array Index von Modul innerhalb von modules
//             "terminal": "relationWire_0", ➡️ irrelevant
//             "uid": "reactflow__edge-dndnode_0rel-dndnode_0-364bc27b-ad04-42e3-a548-792a8e54efcf-dndnode_1cmn-dndnode_1" => edges.id
//         },
//         "tgt": {
//             "moduleId": 1, ➡️ array Index von Modul innerhalb von modules
//             "terminal": "SOURCES", ➡️ irrelevant
//             "uid": "dndnode_0"
//         }
//     }
// ]

// to
// "edges": [
// {
//     "source": "dndnode_0",
//     "sourceHandle": "rel-dndnode_0-364bc27b-ad04-42e3-a548-792a8e54efcf",
//     "target": "dndnode_1",
//     "targetHandle": "cmn-dndnode_1",
//     "id": "reactflow__edge-dndnode_0rel-dndnode_0-364bc27b-ad04-42e3-a548-792a8e54efcf-dndnode_1cmn-dndnode_1"
// }
function convertRelationsToReactFlowRelations(wires, modules) {
    console.log("relations wires: ", wires);

    const edges = wires.map(wire => {
        console.log("wire", wire);

        if (!wire.src || !wire.tgt) {
            console.error('Fehler: Einige Wire-Objekte haben keine vollständigen src oder tgt Daten.', wire);
            return null;
        }

        // Finden der entsprechenden Module anhand der moduleId
        const sourceModule = modules[wire.src.moduleId];
        const targetModule = modules[wire.tgt.moduleId];

        if (!sourceModule || !targetModule) {
            console.error('Fehler: Modul nicht gefunden für moduleId', wire.src.moduleId, wire.tgt.moduleId);
            return null;
        }

        // Generierung der Source und Target IDs
        const source = `dndnode_${wire.src.moduleId}`;
        const target = `dndnode_${wire.tgt.moduleId}`;

        // Extraktion der Handle-Informationen
        const srcUidParts = wire.src.uid?.split("-");
        const sourceHandle = srcUidParts.slice(1, 3).join('-');
        const targetHandle = wire.tgt.terminal?.toLowerCase();

        // Generierung der Edge-Id aus der Quell-UID
        const edgeId = wire.src.uid;

        // Überprüfen, ob alle benötigten Daten vorhanden sind
        if (!source || !target || !sourceHandle || !targetHandle || !edgeId) {
            console.error('Fehler: Nicht alle erforderlichen Daten konnten aus dem Wire-Objekt extrahiert werden.', wire);
            return null;
        }

        // Erstellen des Edge-Objekts
        return {
            id: edgeId,
            source: source,
            sourceHandle: sourceHandle,
            target: target,
            targetHandle: targetHandle
        };
    });

    console.log("edges from method: ", edges);
    return edges.filter(edge => edge !== null);
}

export default convertRelationsToReactFlowRelations;
