import React, { useCallback, useEffect, useMemo, useRef, useState, useContext } from 'react';
import ReactFlow, {
    MiniMap,
    ReactFlowProvider,
    Background,
    addEdge,
    Controls,
    SelectionMode,
} from 'reactflow';
import 'reactflow/dist/style.css';
import { CustomModelNode } from './CustomModelNode';
import Sidebar from './Sidebar';
import {NodesContext, EdgesContext, CustomModelNodeIndexContext} from "../../App";
import ConnectionLine from './Connections/ConnectionLine';

export const ReactFlowComponent = (props) => {
    const {customModelNodeIndex, setCustomModelNodeIndex} = useContext(CustomModelNodeIndexContext);
    const {localCustomModelNodeIndex, setLocalCustomModelNodeIndex} = useContext(CustomModelNodeIndexContext);

    const getId = () => {
        let newId;
        setCustomModelNodeIndex(prevIndex => {
            newId = `dndnode_${prevIndex}`;
            return prevIndex + 1;
        });
        return newId;
    }

    const {nodes, setNodes, onNodesChange} = useContext(NodesContext);
    const {edges, setEdges, onEdgesChange} = useContext(EdgesContext);

    const reactFlowWrapper = useRef(null);
    const nodeTypes = useMemo(() => ({ customModel: CustomModelNode }), []);
    const [reactFlowInstance, setReactFlowInstance] = useState(null);

    const onConnect = useCallback(
        (connection) => setEdges((eds) => addEdge(connection, eds)),
        [setEdges]
    );

    useEffect(() => {
        setReactFlowInstance(props.reactFlowInstance);
    }, [props.reactFlowInstance]);

    const onDragOver = useCallback((event) => {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';
    }, []);

    const nodeColor = (node) => {
        switch (node.type) {
            case 'customModel':
                return '#ff8700';
            case 'output':
                return '#6865A5';
            default:
                return '#ff0072';
        }
    };

    const onDrop = useCallback(
        (event) => {
            event.preventDefault();
            const reactFlowBounds = reactFlowWrapper.current.getBoundingClientRect();
            const type = event.dataTransfer.getData('application/reactflow');
            if (typeof type === 'undefined' || !type) {
                console.log('type undefined');
                return;
            }
            const position = reactFlowInstance.project({
                x: event.clientX - reactFlowBounds.left,
                y: event.clientY - reactFlowBounds.top,
            });
            const data = {
                label: "",
                objectType: "",
                isAggregateRoot: false,
                controllerScope: "Frontend",
                enableSorting: false,
                addDeletedField: true,
                addHiddenField: true,
                addStarttimeEndtimeFields: true,
                enableCategorization: false,
                description: "",
                mapToExistingTable: "",
                extendExistingModelClass: "",
                actions: {
                    actionIndex: false,
                    actionList: false,
                    actionShow: false,
                    actionNewCreate: false,
                    actionEditUpdate: false,
                    actionDelete: false,
                },
                customActions: [
                ],
                properties: [],
                relations: [],
            };
            const newNode = {
                id: getId(),
                type,
                position,
                data,
                dragHandle: '.drag-handle',
                draggable: true,
            };
            setNodes((nds) => nds.concat(newNode));
            // console.log(newNode)
        },
        [reactFlowInstance, setNodes]
    );

    return (
        <div style={{ width: '100%', height: 'calc(100vh - 45px)' }} className="dndflow">
            <ReactFlowProvider>
                <div className="reactflow-wrapper" ref={reactFlowWrapper}>
                    <ReactFlow
                        nodes={nodes}
                        edges={edges}
                        onNodesChange={onNodesChange}
                        onEdgesChange={onEdgesChange}
                        onConnect={onConnect}
                        nodeTypes={nodeTypes}
                        selectionMode={SelectionMode.Partial}
                        onInit={setReactFlowInstance}
                        onDrop={onDrop}
                        onDragOver={onDragOver}
                        connectionLineComponent={ConnectionLine}
                        removeNode={props.removeNode}
                    >
                        <MiniMap nodeColor={nodeColor} nodeStrokeWidth={3} zoomable pannable />
                        <Controls showInteractive={false} />
                        <Background variant="cross" />
                    </ReactFlow>
                </div>
                <Sidebar nodes={props.nodes} />
            </ReactFlowProvider>
        </div>
    );
}
