import React from 'react';

export default ({ fromX, fromY, toX, toY }) => {
    return (
        <g>
            <path
                fill="none"
                stroke="#f49700"
                strokeWidth={2.5}
                className="animated"
                d={`M${fromX},${fromY} C ${fromX} ${toY} ${fromX} ${toY} ${toX},${toY}`}
            />
            <circle
                cx={toX}
                cy={toY}
                fill="#f49700"
                r={3}
                stroke="#f49700"
                strokeWidth={2.5}
            />
        </g>
    );
};
