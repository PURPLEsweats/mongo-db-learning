<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoundWave Records — Data Explorer</title>
    <script src="https://unpkg.com/vis-network@9.1.9/dist/vis-network.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0d1117;
            color: #e6edf3;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(135deg, #1a1f2e 0%, #0d1117 100%);
            border-bottom: 1px solid #30363d;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-shrink: 0;
        }

        header h1 {
            font-size: 1.4rem;
            background: linear-gradient(90deg, #FF6B6B, #4ECDC4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stats {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-left: auto;
        }

        .stat-chip {
            font-size: 0.72rem;
            padding: 3px 10px;
            border-radius: 12px;
            border: 1px solid #30363d;
            background: #161b22;
            color: #8b949e;
        }

        .stat-chip span {
            color: #e6edf3;
            font-weight: 600;
        }

        .main {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        #graph-container {
            flex: 1;
            position: relative;
        }

        #network {
            width: 100%;
            height: 100%;
        }

        .legend {
            position: absolute;
            bottom: 16px;
            left: 16px;
            background: rgba(13, 17, 23, 0.9);
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 12px 14px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px 16px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.75rem;
            color: #8b949e;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        #detail-panel {
            width: 300px;
            background: #161b22;
            border-left: 1px solid #30363d;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            overflow: hidden;
        }

        #detail-header {
            padding: 16px;
            border-bottom: 1px solid #30363d;
            background: #0d1117;
        }

        #detail-type {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #8b949e;
            margin-bottom: 4px;
        }

        #detail-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #e6edf3;
        }

        #detail-type-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 6px;
        }

        #detail-body {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
        }

        .detail-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 12px;
        }

        .detail-key {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #8b949e;
            margin-bottom: 2px;
        }

        .detail-val {
            font-size: 0.88rem;
            color: #e6edf3;
            line-height: 1.4;
        }

        #detail-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #484f58;
            font-size: 0.9rem;
            text-align: center;
            padding: 24px;
            gap: 8px;
        }

        #detail-placeholder svg {
            opacity: 0.3;
        }

        .controls {
            position: absolute;
            top: 16px;
            right: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .ctrl-btn {
            background: rgba(22, 27, 34, 0.9);
            border: 1px solid #30363d;
            color: #8b949e;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: background 0.15s, color 0.15s;
        }

        .ctrl-btn:hover {
            background: #21262d;
            color: #e6edf3;
        }
    </style>
</head>
<body>

<header>
    <h1>SoundWave Records</h1>
    <span style="color:#484f58; font-size:0.85rem;">Data Explorer</span>
    <div class="stats">
        <?php foreach ($counts as $label => $count): ?>
            <div class="stat-chip"><span><?= $count ?></span> <?= $label ?></div>
        <?php endforeach; ?>
    </div>
</header>

<div class="main">
    <div id="graph-container">
        <div id="network"></div>

        <div class="legend">
            <?php foreach ($typeColors as $type => $color): ?>
                <div class="legend-item">
                    <div class="legend-dot" style="background:<?= $color ?>"></div>
                    <?= $type ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="controls">
            <button class="ctrl-btn" onclick="network.fit()">Fit View</button>
            <button class="ctrl-btn" onclick="network.setOptions({physics:{enabled:true}})">Shake</button>
        </div>
    </div>

    <div id="detail-panel">
        <div id="detail-placeholder">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/>
            </svg>
            Click any node<br>to see its details
        </div>
        <div id="detail-content" style="display:none; flex-direction:column; height:100%;">
            <div id="detail-header">
                <div id="detail-type"></div>
                <div id="detail-name"></div>
                <span id="detail-type-badge"></span>
            </div>
            <div id="detail-body"></div>
        </div>
    </div>
</div>

<script>
const nodes   = new vis.DataSet(<?= $nodesJson ?>);
const edges   = new vis.DataSet(<?= $edgesJson ?>);
const details = <?= $detailsJson ?>;

const typeColors = <?= json_encode($typeColors) ?>;

const container = document.getElementById('network');
const network = new vis.Network(container, { nodes, edges }, {
    physics: {
        enabled: true,
        solver: 'forceAtlas2Based',
        forceAtlas2Based: {
            gravitationalConstant: -60,
            centralGravity: 0.003,
            springLength: 120,
            springConstant: 0.04,
            damping: 0.6,
        },
        stabilization: { iterations: 200 },
    },
    edges: {
        color: { color: '#30363d', highlight: '#58a6ff' },
        smooth: { type: 'curvedCW', roundness: 0.2 },
        font: { color: '#8b949e', strokeWidth: 0 },
        width: 1.5,
    },
    nodes: {
        borderWidth: 2,
        borderWidthSelected: 3,
        font: { color: '#e6edf3', face: 'Segoe UI, system-ui, sans-serif' },
    },
    interaction: {
        hover: true,
        tooltipDelay: 0,
        navigationButtons: false,
    },
});

network.on('click', ({ nodes: clicked }) => {
    if (!clicked.length) return;

    const id    = clicked[0];
    const d     = details[id];
    const type  = d['__type'];
    const label = d['__label'];

    document.getElementById('detail-placeholder').style.display = 'none';
    const content = document.getElementById('detail-content');
    content.style.display = 'flex';

    document.getElementById('detail-type').textContent = 'Document type';
    document.getElementById('detail-name').textContent = label;

    const badge = document.getElementById('detail-type-badge');
    badge.textContent = type;
    badge.style.background = typeColors[type] + '33';
    badge.style.color = typeColors[type];
    badge.style.border = '1px solid ' + typeColors[type] + '66';

    const body = document.getElementById('detail-body');
    body.innerHTML = '';
    for (const [k, v] of Object.entries(d)) {
        if (k.startsWith('__')) continue;
        const row = document.createElement('div');
        row.className = 'detail-row';
        row.innerHTML = `<div class="detail-key">${k}</div><div class="detail-val">${v}</div>`;
        body.appendChild(row);
    }
});

network.on('stabilizationIterationsDone', () => {
    network.setOptions({ physics: { enabled: false } });
});
</script>

</body>
</html>
