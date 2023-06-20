import React, {useCallback, useEffect, useMemo, useRef, useState} from 'react';
import ReactFlow, {
    MiniMap,
    ReactFlowProvider,
    Background,
    addEdge,
    Controls,
    SelectionMode, applyNodeChanges, applyEdgeChanges
} from 'reactflow';
import 'reactflow/dist/style.css';
import {CustomModelNode} from "./CustomModelNode";
import Sidebar from "./Sidebar";

const panOnDrag = [1, 2];

let id = 0;
const getId = () => `dndnode_${id++}`;

export const ReactFlowComponent = (props) => {
    const initialNodes = [];

    const initialEdges = [];

    const reactFlowWrapper = useRef(null);
    const nodeTypes = useMemo(() => ({ customModel: CustomModelNode }), []);
    const [reactFlowInstance, setReactFlowInstance] = useState(null);

    const [nodes, setNodes] = useState(initialNodes);
    const [edges, setEdges] = useState(initialEdges);

    const onNodesChange = useCallback(
        (changes) => setNodes((nds) => applyNodeChanges(changes, nds)),
        [setNodes]
    );
    const onEdgesChange = useCallback(
        (changes) => setEdges((eds) => applyEdgeChanges(changes, eds)),
        [setEdges]
    );
    const onConnect = useCallback(
        (connection) => setEdges((eds) => addEdge(connection, eds)),
        [setEdges]
    );

    useEffect(() => {
        console.log("use effect for nodes in ReactFlowComponent");
    }, [nodes]);

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

            // check if the dropped element is valid
            if (typeof type === 'undefined' || !type) {
                console.log("type undefined");
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
            console.log(newNode);

            setNodes((nds) => nds.concat(newNode));
        },
        [reactFlowInstance]
    );

    return (
        <div style={{ width: '100%', height: '100vh' }} className="dndflow">
            <ReactFlowProvider>
                <div className="reactflow-wrapper" ref={reactFlowWrapper}>
                    <ReactFlow
                        nodes={nodes}
                        edges={edges}
                        onNodesChange={onNodesChange}
                        onEdgesChange={onEdgesChange}
                        onConnect={onConnect}
                        nodeTypes={nodeTypes}
                        panOnDrag={panOnDrag}
                        selectionMode={SelectionMode.Partial}
                        onInit={setReactFlowInstance}
                        onDrop={onDrop}
                        onDragOver={onDragOver}
                    >
                        <MiniMap
                            nodeColor={nodeColor}
                            nodeStrokeWidth={3}
                            zoomable
                            pannable
                        />
                        <Controls showInteractive={false} />
                        <Background variant="cross" />
                    </ReactFlow>
                </div>
                <Sidebar
                    nodes={nodes}
                />
            </ReactFlowProvider>
        </div>
    )
}
