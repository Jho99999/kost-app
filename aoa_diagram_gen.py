"""
Generate AOA drawio XML — event nodes + activity arrows
"""
from xml.sax.saxutils import escape

ID = [200]

def nid():
    ID[0] += 1
    return str(ID[0])

cells = []

def add(o):
    cells.append(o)

# Header
add('<?xml version="1.0" encoding="UTF-8"?>')
add('<mxfile host="app.diagrams.net">')
add('<diagram name="AOA - Sistem Informasi Manajemen Kost" id="aoa-kost">')
add('<mxGraphModel dx="3967" dy="2460" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1200" pageHeight="850" math="0" shadow="0">')
add('<root>')
add('<mxCell id="0"/>')
add('<mxCell id="1" parent="0"/>')

# Define event nodes
# (id, label, x, y)
events = [
    ("1", "1", 60, 200),
    ("2", "2", 250, 200),
    ("3", "3", 430, 200),
    ("4", "4", 620, 200),
    ("5", "5", 860, 200),
    ("6", "6", 860, 360),
    ("7", "7", 1120, 200),
    ("8", "8", 1360, 200),
    ("9", "9", 1120, 360),
    ("10", "10", 1600, 200),
    ("11", "11", 1860, 120),
    ("12", "12", 1860, 300),
]

eid_map = {}
for eid, label, x, y in events:
    cid = nid()
    eid_map[eid] = cid
    add(f'<mxCell id="{cid}" parent="1" style="ellipse;whiteSpace=wrap;html=1;fontSize=16;fillColor=none;" value="{label}" vertex="1">')
    add(f'<mxGeometry x="{x}" y="{y}" width="40" height="40" as="geometry"/>')
    add('</mxCell>')

# Define activity arrows
# (from_event, to_event, label, dashed, source_x_off, source_y_off, target_x_off, target_y_off)
arrows = [
    # Phase 1
    ("1", "2", "A1 (3)", False, None, None, None, None),
    ("2", "3", "A2 (3)", False, None, None, None, None),
    ("3", "4", "A3 (3)", False, None, None, None, None),
    ("1", "4", "A4 (1)", False, None, None, None, None),
    # Phase 2
    ("4", "5", "B1 (15)", False, None, None, None, None),
    ("4", "6", "B2 (4)", False, None, None, None, None),
    ("6", "5", "D1 (0)", True, None, None, None, None),
    # Phase 3
    ("5", "7", "C1 (22)", False, None, None, None, None),
    ("7", "8", "C2 (23)", False, None, None, None, None),
    ("5", "9", "C3 (12)", False, None, None, None, None),
    ("9", "8", "D2 (0)", True, None, None, None, None),
    # Phase 4
    ("8", "10", "D3 (12)", False, None, None, None, None),
    # Phase 5
    ("10", "11", "E1 (2)", False, None, None, None, None),
    ("10", "12", "E2 (3)", False, None, None, None, None),
    ("11", "12", "D4 (0)", True, None, None, None, None),
]

def make_edge_style(dashed=False, source_arrow="1;", target_arrow="1;"):
    style = "edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;"
    style += f"exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;"
    style += "startArrow=none;startFill=0;endArrow=block;endFill=0;"
    if dashed:
        style += "dashed=1;"
    style += "fontSize=14;"
    return style

for fr, to, label, dashed, sx, sy, tx, ty in arrows:
    cid = nid()
    style = make_edge_style(dashed)
    add(f'<mxCell id="{cid}" parent="1" style="{style}" value="{escape(label)}" edge="1" source="{eid_map[fr]}" target="{eid_map[to]}">')
    add(f'<mxGeometry relative="1" as="geometry"/>')
    add('</mxCell>')

# Close
add('</root>')
add('</mxGraphModel>')
add('</diagram>')
add('</mxfile>')

xml = '\n'.join(cells)
path = r"E:\UNIKOM\semester 6\Manajemen Proyek Perangkat Lunak\TUBES\AON\AOA - Sistem Informasi Manajemen Kost.drawio"
from hermes_tools import write_file
write_file(path, xml)
print(f"✅ AOA diagram saved: {path}")
print(f"   {len(events)} event nodes, {len(arrows)} arrows ({sum(1 for *_,d,_ in arrows if not d)} activities + {sum(1 for *_,d,_ in arrows if d)} dummies)")
