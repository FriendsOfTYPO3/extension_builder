    // from
    // "wires": [
    //     {
    //         "src": {
    //             "moduleId": 0, ➡️ array Index von Modul innerhalb von modules
    //             "terminal": "relationWire_0", ➡️ irrelevant
    //             "uid": "reactflow__edge-dndnode_0rel-dndnode_0-364bc27b-ad04-42e3-a548-792a8e54efcf-dndnode_1cmn-dndnode_1"
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
function convertRelationsToReactFlowRelations(wires) {

    console.log("relations", wires);

    const edges = wires.map(wire => {
        console.log("wire", wire);

        if (!wire.src || !wire.tgt) {
            console.error('Fehler: Einige Wire-Objekte haben keine vollständigen src oder tgt Daten.', wire);
            return null;
        }

        // Extraktion der Source-Informationen
        const srcUidParts = wire.src.uid?.split("-");
        const sourceMatch = srcUidParts[0].match(/dndnode_\d+/);
        const source = sourceMatch ? sourceMatch[0] : null;
        const sourceHandle = srcUidParts.slice(1, 3).join('-');

        // Extraktion der Target-Informationen
        const targetMatch = wire.tgt.uid?.match(/dndnode_\d+/);
        const target = targetMatch ? targetMatch[0] : null;
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

    console.log("edges from method", edges);
    return edges.filter(edge => edge !== null);
}

export default convertRelationsToReactFlowRelations;
