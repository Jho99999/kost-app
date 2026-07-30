"""
FULL REBUILD of D.3 Network Diagram section.
Removes everything from "D.3 Network Diagram" to "D.4 Gantt Chart"
and rebuilds with clean data.
"""
from docx import Document
from docx.shared import Pt, RGBColor, Cm
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml.ns import qn, nsdecls
from docx.oxml import parse_xml

DOCX_PATH = r"E:\UNIKOM\semester 6\Manajemen Proyek Perangkat Lunak\TUBES\SPMP - Sistem Informasi Manajemen Kost.docx"

doc = Document(DOCX_PATH)
body = doc.element.body

# ===== FIND SECTION BOUNDARIES =====
start_elem = None
end_elem = None
for para in doc.paragraphs:
    if "D.3 Network Diagram" in para.text:
        start_elem = para._element
    if "D.4 Gantt Chart" in para.text:
        end_elem = para._element
        break

if start_elem is None or end_elem is None:
    print(f"❌ Boundaries: start={'found' if start_elem else 'MISSING'}, end={'found' if end_elem else 'MISSING'}")
    exit(1)

# ===== REMOVE ALL CONTENT BETWEEN D.3 AND D.4 =====
children = list(body)
in_range = False
removed = 0
for child in children:
    if child is start_elem:
        in_range = True
        continue
    if child is end_elem:
        break
    if in_range:
        tag = child.tag.split('}')[-1] if '}' in child.tag else child.tag
        if tag in ['p', 'tbl']:
            try:
                body.remove(child)
                removed += 1
            except:
                pass

print(f"Removed {removed} elements between D.3 and D.4")

# ===== HELPER FUNCTIONS =====
def make_p(text, style=None, size=11, bold=False, space_after=120):
    """Create a paragraph element"""
    p_xml = f'<w:p {nsdecls("w")}><w:pPr>'
    if style:
        p_xml += f'<w:pStyle w:val="{style}"/>'
    p_xml += f'<w:spacing w:after="{space_after}"/>'
    p_xml += '</w:pPr>'
    p_xml += f'<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/>'
    p_xml += f'<w:sz w:val="{size*2}"/>'
    if bold:
        p_xml += '<w:b/>'
    p_xml += '</w:rPr>'
    p_xml += f'<w:t xml:space="preserve">{escape_xml(text)}</w:t>'
    p_xml += '</w:r></w:p>'
    return parse_xml(p_xml)

def escape_xml(s):
    return s.replace("&","&amp;").replace("<","&lt;").replace(">","&gt;").replace('"',"&quot;")

def make_p_mixed(parts, space_after=120):
    """Make paragraph with mixed formatting. parts=[(text,bold,size), ...]"""
    runs_xml = ""
    for text, bold, size in parts:
        runs_xml += f'<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/>'
        runs_xml += f'<w:sz w:val="{size*2}"/>'
        if bold:
            runs_xml += '<w:b/>'
        runs_xml += '</w:rPr>'
        runs_xml += f'<w:t xml:space="preserve">{escape_xml(text)}</w:t></w:r>'
    p_xml = f'<w:p {nsdecls("w")}><w:pPr><w:spacing w:after="{space_after}"/></w:pPr>{runs_xml}</w:p>'
    return parse_xml(p_xml)

def make_cell_xml(text, bold=False, size=8, align='center', shade=None):
    """Return tuple (cell_xml, cell_width_xml) for a table cell"""
    halign = {'center':'center','left':'left','right':'right'}.get(align,'center')
    cell_xml = '<w:tc>'
    cell_xml += '<w:tcPr>'
    cell_xml += '<w:tcBorders>'
    for side in ['top','left','bottom','right']:
        cell_xml += f'<w:{side} w:val="single" w:sz="4" w:space="0" w:color="000000"/>'
    cell_xml += '</w:tcBorders>'
    cell_xml += '<w:vAlign w:val="center"/>'
    cell_xml += '<w:shd w:fill="' + (shade or "FFFFFF") + '" w:val="clear"/>'
    cell_xml += '</w:tcPr>'
    cell_xml += '<w:p><w:pPr><w:jc w:val="' + halign + '"/></w:pPr>'
    cell_xml += '<w:r><w:rPr>'
    cell_xml += '<w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/>'
    cell_xml += f'<w:sz w:val="{size*2}"/>'
    if bold:
        cell_xml += '<w:b/>'
    cell_xml += '</w:rPr>'
    cell_xml += f'<w:t xml:space="preserve">{escape_xml(str(text))}</w:t>'
    cell_xml += '</w:r></w:p></w:tc>'
    return parse_xml(cell_xml)

def make_table(headers, rows, col_widths_twips=None):
    """Create a table XML element from headers and data rows.
    rows = list of (cell_values_list, row_shade_color_or_None)"""
    ncols = len(headers)
    nrows = len(rows) + 1
    
    tbl_xml = f'<w:tbl {nsdecls("w")}>'
    # Table grid
    tbl_xml += '<w:tblPr>'
    tbl_xml += '<w:tblStyle w:val="TableGrid"/>'
    tbl_xml += '<w:tblW w:w="5000" w:type="pct"/>'
    tbl_xml += '<w:jc w:val="center"/>'
    tbl_xml += '</w:tblPr>'
    tbl_xml += '<w:tblGrid>'
    for w in col_widths_twips or [1000]*ncols:
        tbl_xml += f'<w:gridCol w:w="{w}"/>'
    tbl_xml += '</w:tblGrid>'
    
    # Header row
    tbl_xml += '<w:tr>'
    for h in headers:
        tbl_xml += '<w:tc>'
        tbl_xml += '<w:tcPr><w:tcBorders>'
        for side in ['top','left','bottom','right']:
            tbl_xml += f'<w:{side} w:val="single" w:sz="4" w:space="0" w:color="000000"/>'
        tbl_xml += '</w:tcBorders><w:vAlign w:val="center"/>'
        tbl_xml += '<w:shd w:fill="D9E2F3" w:val="clear"/>'
        tbl_xml += '</w:tcPr>'
        tbl_xml += '<w:p><w:pPr><w:jc w:val="center"/></w:pPr>'
        tbl_xml += '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/>'
        tbl_xml += '<w:sz w:val="16"/><w:b/></w:rPr>'
        tbl_xml += f'<w:t>{escape_xml(h)}</w:t></w:r></w:p></w:tc>'
    tbl_xml += '</w:tr>'
    
    # Data rows
    for r_data, row_shade in rows:
        tbl_xml += '<w:tr>'
        for ci, val in enumerate(r_data):
            shade = row_shade or ("" if row_shade is None else "FFFFFF")
            tbl_xml += '<w:tc>'
            tbl_xml += '<w:tcPr><w:tcBorders>'
            for side in ['top','left','bottom','right']:
                tbl_xml += f'<w:{side} w:val="single" w:sz="4" w:space="0" w:color="000000"/>'
            tbl_xml += '</w:tcBorders><w:vAlign w:val="center"/>'
            if row_shade:
                tbl_xml += f'<w:shd w:fill="{row_shade}" w:val="clear"/>'
            tbl_xml += '</w:tcPr>'
            hal = 'center' if ci != 1 else 'left'
            tbl_xml += f'<w:p><w:pPr><w:jc w:val="{hal}"/></w:pPr>'
            tbl_xml += '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/>'
            tbl_xml += '<w:sz w:val="16"/></w:rPr>'
            tbl_xml += f'<w:t>{escape_xml(str(val))}</w:t></w:r></w:p></w:tc>'
        tbl_xml += '</w:tr>'
    
    tbl_xml += '</w:tbl>'
    return parse_xml(tbl_xml)

# ===== REBUILD CONTENT =====
# Insert right before end_elem (D.4 Gantt Chart)
end_parent = end_elem.getparent()
# We'll use addprevious to insert before end_elem
def insert_before(ref_elem, new_elem):
    ref_elem.addprevious(new_elem)

# ── AON NARRATIVE ──
narr_aon = [
    ("D.3 Network Diagram (AON/AOA)", "Heading3", 11, True),
    ("Definisi AON (Activity on Node)", "Heading4", 11, True),
    ("AON (Activity on Node) adalah metode penjadwalan proyek di mana setiap aktivitas direpresentasikan sebagai node (kotak) dan panah menunjukkan hubungan ketergantungan antar aktivitas [1]. Dalam perhitungan CPM, terdapat dua tahapan:", None, 11, False),
    ("1. Forward Pass (Perhitungan Mundur): Menentukan Earliest Start (ES) dan Earliest Finish (EF) dari setiap aktivitas. ES aktivitas pertama = 0. EF = ES + Durasi. ES aktivitas berikutnya = EF terbesar dari predecessor-nya.", None, 11, False),
    ("2. Backward Pass (Perhitungan Maju): Menentukan Latest Start (LS) dan Latest Finish (LF). LF aktivitas terakhir = EF-nya. LS = LF − Durasi. LF predecessor = LS terkecil dari successor-nya.", None, 11, False),
    ("Float (slack) = LS − ES. Aktivitas dengan float = 0 berada pada jalur kritis (critical path) dan tidak boleh ditunda tanpa memperpanjang durasi proyek.", None, 11, False),
    ("Jalur Kritis (Critical Path)", "Heading4", 11, True),
    ("Berdasarkan perhitungan forward dan backward pass, jalur kritis proyek ini adalah:", None, 11, False),
]

for text, style, size, bold in narr_aon:
    if "\n" in text:
        for line in text.split("\n"):
            elem = make_p(line, style, size, bold)
            insert_before(end_elem, elem)
    else:
        elem = make_p(text, style, size, bold)
        insert_before(end_elem, elem)

# Critical path text
cp_text = "A1 → A2 → A3 → B1 → C1 → C2 → D1 → E2 → END"
cp_p = make_p_mixed([
    ("Jalur kritis: ", False, 11),
    (cp_text, True, 11),
    (" dengan total durasi 84 hari kerja.", False, 11)
])
insert_before(end_elem, cp_p)

# ── AON TABLE (Forward/Backward Pass) ──
aon_header = make_p("Tabel Forward/Backward Pass AON", "Heading4", 11, True)
insert_before(end_elem, aon_header)

aon_data = [
    # (No, Node, Nama, Dur, ES, EF, LS, LF, Float)
    ("1","A1","Inisiasi & Identifikasi","3","0","3","0","3","0"),
    ("2","A2","Fitur & Perencanaan","3","3","6","3","6","0"),
    ("3","A3","Risiko & SPMP","3","6","9","6","9","0"),
    ("4","A4","AI Cari Referensi","1","0","1","8","9","8"),
    ("5","B1","Desain UI/UX","15","9","24","9","24","0"),
    ("6","B2","Setup Lingkungan","4","9","13","20","24","11"),
    ("7","C1","Frontend","22","24","46","24","46","0"),
    ("8","C2","Backend","23","46","69","46","69","0"),
    ("9","C3","Database","12","24","36","57","69","33"),
    ("10","D1","Pengujian & UAT","12","69","81","69","81","0"),
    ("11","E1","Deployment & SSL","2","81","83","82","84","1"),
    ("12","E2","Pelatihan & Dok.","3","81","84","81","84","0"),
]

aon_rows = []
for row in aon_data:
    shade = "E2EFDA" if row[8] == "0" else "FCE4EC"
    crit_text = "Ya" if row[8] == "0" else "Tidak"
    aon_rows.append((list(row) + [crit_text], shade))

aon_tbl = make_table(
    ["No","Node","Nama Aktivitas","Dur","ES","EF","LS","LF","Float","Kritis?"],
    aon_rows,
    [700, 800, 3000, 700, 700, 700, 700, 700, 700, 700]
)
insert_before(end_elem, aon_tbl)
insert_before(end_elem, make_p("", None, 6, False))

# ── AON PREDECESSOR TABLE ──
pred_header = make_p("Tabel Predecessor AON", "Heading4", 11, True)
insert_before(end_elem, pred_header)

pred_data = [
    ("1","A1","Inisiasi & Identifikasi","3","—"),
    ("2","A2","Fitur & Perencanaan","3","A1"),
    ("3","A3","Risiko & SPMP","3","A2"),
    ("4","A4","AI Cari Referensi","1","—"),
    ("5","B1","Desain UI/UX","15","A3"),
    ("6","B2","Setup Lingkungan","4","A3"),
    ("7","C1","Frontend","22","B1"),
    ("8","C2","Backend","23","C1"),
    ("9","C3","Database","12","B1"),
    ("10","D1","Pengujian & UAT","12","C2, C3"),
    ("11","E1","Deployment & SSL","2","D1"),
    ("12","E2","Pelatihan & Dok.","3","D1"),
]

pred_rows = [(row, None) for row in pred_data]
pred_tbl = make_table(
    ["No","Node","Nama Aktivitas","Durasi","Predecessor"],
    pred_rows,
    [700, 800, 3000, 1000, 1500]
)
insert_before(end_elem, pred_tbl)
insert_before(end_elem, make_p("", None, 6, False))

# ── AOA NARRATIVE ──
narr_aoa = [
    ("Diagram AOA (Activity on Arrow)", "Heading4", 11, True),
    ("Berbeda dengan AON, diagram AOA (Activity on Arrow) merepresentasikan aktivitas sebagai panah (arrow) yang menghubungkan dua node lingkaran yang menandai event (kejadian). Setiap event menandai dimulai atau selesainya satu atau lebih aktivitas. Dummy activity (panah putus-putus dengan durasi 0) digunakan untuk menunjukkan ketergantungan antar aktivitas tanpa menambah durasi [3].", None, 11, False),
]

for text, style, size, bold in narr_aoa:
    elem = make_p(text, style, size, bold)
    insert_before(end_elem, elem)

# Combined description paragraph
desc_p = make_p_mixed([
    ("Diagram AOA proyek ini memiliki 12 event (A–L) dan 12 aktivitas (panah solid), ditambah 3 dummy activity (panah putus-putus) untuk mengatur merge jalur paralel. Jalur kritis melewati event: ", False, 11),
    ("A → B → C → D → E → G → H → J → L", True, 11),
    (" dengan aktivitas: ", False, 11),
    ("A1 → A2 → A3 → B1 → C1 → C2 → D3 → E2", True, 11),
    (" (total 84 hari kerja).", False, 11),
])
insert_before(end_elem, desc_p)

extra_p = make_p("Tiga dummy activity (D1, D2, D4) digunakan untuk menggabungkan jalur paralel yang memiliki float: B2 (float 11), C3 (float 33), dan E1 (float 1) kembali ke jalur kritis pada event yang sesuai.", None, 11, False)
insert_before(end_elem, extra_p)

# ── AOA TABLE ──
aoa_header = make_p("Tabel Perhitungan AOA", "Heading4", 11, True)
insert_before(end_elem, aoa_header)

aoa_data = [
    ("A1","Inisiasi & Identifikasi","3","0 (H-0)","3 (H-3)","Ya"),
    ("A2","Fitur & Perencanaan","3","3 (H-3)","6 (H-6)","Ya"),
    ("A3","Risiko & SPMP","3","6 (H-6)","9 (H-9)","Ya"),
    ("A4","AI Cari Referensi","1","0 (H-0)","1 (H-1)","Tidak"),
    ("B1","Desain UI/UX (Admin & Pengguna)","15","9 (H-9)","24 (H-24)","Ya"),
    ("B2","Setup Lingkungan (Laravel + Hosting)","4","9 (H-9)","13 (H-13)","Tidak"),
    ("D1","Dummy (B2→B1)","0","13 (H-13)","24 (H-24)","Tidak"),
    ("C1","Frontend","22","24 (H-24)","46 (H-46)","Ya"),
    ("C2","Backend","23","46 (H-46)","69 (H-69)","Ya"),
    ("C3","Database","12","24 (H-24)","36 (H-36)","Tidak"),
    ("D2","Dummy (C3→C2)","0","36 (H-36)","69 (H-69)","Tidak"),
    ("D3","Pengujian & UAT","12","69 (H-69)","81 (H-81)","Ya"),
    ("E1","Deployment & SSL","2","81 (H-81)","83 (H-83)","Tidak"),
    ("E2","Pelatihan & Dokumentasi","3","81 (H-81)","84 (H-84)","Ya"),
    ("D4","Dummy (E1→E2)","0","83 (H-83)","84 (H-84)","Tidak"),
]

aoa_rows = []
for row in aoa_data:
    shade = "E2EFDA" if row[5] == "Ya" else "FCE4EC"
    aoa_rows.append((row, shade))

aoa_tbl = make_table(
    ["ID","Deskripsi Aktivitas","Durasi","Mulai (ES)","Selesai (EF)","Kritis?"],
    aoa_rows,
    [800, 4000, 1000, 1500, 1500, 1000]
)
insert_before(end_elem, aoa_tbl)
insert_before(end_elem, make_p("", None, 6, False))

doc.save(DOCX_PATH)
print(f"✅ D.3 section rebuilt completely!")
