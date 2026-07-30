"""
Inject E. MANAJEMEN RISIKO with risk & mitigation table
"""
from docx import Document
from docx.shared import Pt
from docx.oxml.ns import nsdecls
from docx.oxml import parse_xml

DOCX_PATH = r"E:\UNIKOM\semester 6\Manajemen Proyek Perangkat Lunak\TUBES\SPMP - Sistem Informasi Manajemen Kost.docx"
doc = Document(DOCX_PATH)
body = doc.element.body

# ─── FIND BOUNDARIES ───
e_elem = None
f_elem = None
for para in doc.paragraphs:
    if "E. MANAJEMEN RISIKO" in para.text:
        e_elem = para._element
    if "F. RENCANA ANGGARAN" in para.text:
        f_elem = para._element
        break

if not e_elem or not f_elem:
    print("❌ Sections E/F not found")
    exit(1)

# ─── REMOVE ALL CONTENT BETWEEN E AND F ───
children = list(body)
to_remove = []
in_range = False
for child in children:
    if child is e_elem:
        in_range = True
        continue
    if child is f_elem:
        break
    if in_range:
        to_remove.append(child)

for elem in to_remove:
    try: body.remove(elem)
    except: pass
print(f"✅ Removed {len(to_remove)} old E section elements")

# ─── HELPERS ───
def esc(s):
    return s.replace("&","&amp;").replace("<","&lt;").replace(">","&gt;")

def P(text, size=11, bold=False, italic=False):
    p = f'<w:p {nsdecls("w")}><w:pPr><w:spacing w:after="120"/>'
    p += '</w:pPr>'
    p += f'<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="{size*2}"/>'
    if bold: p += '<w:b/>'
    if italic: p += '<w:i/>'
    p += '</w:rPr>'
    p += f'<w:t xml:space="preserve">{esc(text)}</w:t></w:r></w:p>'
    return parse_xml(p)

def H(text, level=3):
    style = {3: "Heading3", 4: "Heading4"}
    p = f'<w:p {nsdecls("w")}><w:pPr><w:pStyle w:val="{style[level]}"/></w:pPr>'
    p += f'<w:r><w:t>{esc(text)}</w:t></w:r></w:p>'
    return parse_xml(p)

def TBL(headers, rows, col_cm=None):
    ncols = len(headers)
    tbl = f'<w:tbl {nsdecls("w")}>'
    tbl += '<w:tblPr><w:tblStyle w:val="TableGrid"/><w:tblW w:w="5000" w:type="pct"/><w:jc w:val="center"/></w:tblPr><w:tblGrid>'
    for w in col_cm or [2]*ncols:
        tbl += f'<w:gridCol w:w="{int(w*567)}"/>'
    tbl += '</w:tblGrid>'
    tbl += '<w:tr>'
    for h in headers:
        tbl += '<w:tc><w:tcPr><w:tcBorders>'
        for s in ['top','left','bottom','right']:
            tbl += f'<w:{s} w:val="single" w:sz="4" w:space="0" w:color="000000"/>'
        tbl += '</w:tcBorders><w:vAlign w:val="center"/><w:shd w:fill="D9E2F3" w:val="clear"/></w:tcPr>'
        tbl += '<w:p><w:pPr><w:jc w:val="center"/></w:pPr>'
        tbl += '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="16"/><w:b/></w:rPr>'
        tbl += f'<w:t>{esc(h)}</w:t></w:r></w:p></w:tc>'
    tbl += '</w:tr>'
    for vals, shade in rows:
        tbl += '<w:tr>'
        for ci, val in enumerate(vals):
            tbl += '<w:tc><w:tcPr><w:tcBorders>'
            for s in ['top','left','bottom','right']:
                tbl += f'<w:{s} w:val="single" w:sz="4" w:space="0" w:color="000000"/>'
            tbl += '</w:tcBorders><w:vAlign w:val="center"/>'
            if shade:
                tbl += f'<w:shd w:fill="{shade}" w:val="clear"/>'
            tbl += '</w:tcPr>'
            hal = 'left' if ci >= 1 else 'center'
            tbl += f'<w:p><w:pPr><w:jc w:val="{hal}"/></w:pPr>'
            tbl += '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="16"/></w:rPr>'
            tbl += f'<w:t>{esc(str(val))}</w:t></w:r></w:p></w:tc>'
        tbl += '</w:tr>'
    tbl += '</w:tbl>'
    return parse_xml(tbl)

def ins(elem):
    f_elem.addprevious(elem)

# ─── CONTENT ───
ins(P("", 6))

# Risk table header
risk_headers = ["No", "Risiko", "Kategori", "Prob.", "Dampak", "Tingkat", "Mitigasi / Penanganan"]

risk_data = [
    ("1", "Anggota tim tidak dapat menyelesaikan task tepat waktu karena tugas kuliah lain", "Jadwal", "Tinggi", "Tinggi", "Tinggi",
     "Sprint planning realistis; buffer 1-2 hari tiap sprint; daily standup untuk memantau progres; rolling task jika diperlukan"),
    ("2", "Perubahan kebutuhan dari pemilik kost di tengah pengembangan", "Lingkup", "Sedang", "Tinggi", "Tinggi",
     "Prioritaskan backlog (Scrum); tunda fitur non-kritis ke sprint berikutnya; dokumentasi perubahan"),
    ("3", "Server VPS down atau gagal konfigurasi saat deployment", "Teknis", "Sedang", "Tinggi", "Tinggi",
     "Gunakan VPS dengan SLA ≥99%; backup database otomatis; dokumentasi langkah deployment; uji coba di staging"),
    ("4", "Bug kritis ditemukan saat UAT menjelang deadline", "Kualitas", "Sedang", "Sedang", "Sedang",
     "Testing bertahap (unit → integration → UAT); alokasi waktu khusus untuk debugging; code review sebelum merge"),
    ("5", "Ketergantungan pada satu anggota (Jeri) untuk sebagian besar task (57 jam)", "SDM", "Tinggi", "Sedang", "Tinggi",
     "Pair programming untuk task kritis; dokumentasi kode; pembagian task ulang jika diperlukan"),
    ("6", "Perangkat lunak open source yang digunakan tiba-tiba tidak kompatibel", "Teknis", "Rendah", "Sedang", "Sedang",
     "Gunakan versi LTS (Long Term Support); catat dependency di composer.json; sediakan alternatif library"),
    ("7", "Sprint tidak selesai sesuai durasi karena estimasi terlalu optimis", "Jadwal", "Sedang", "Sedang", "Sedang",
     "Gunakan velocity tracking dari sprint sebelumnya; sprint retrospective untuk perbaikan estimasi"),
    ("8", "Kehilangan data akibat kegagalan server atau migrasi database", "Teknis", "Rendah", "Tinggi", "Tinggi",
     "Backup database harian; gunakan migration versi kontrol; uji migrasi di lingkungan staging"),
]

risk_rows = [(row, None) for row in risk_data]

ins(TBL(["No", "Risiko", "Kategori", "Prob.", "Dampak", "Tingkat", "Mitigasi / Penanganan"],
    risk_rows, [0.8, 4.0, 1.5, 1.3, 1.3, 1.3, 5.0]))

# Risk level legend
ins(P("", 6))
ins(P("Prob. = Probabilitas (Tinggi/Sedang/Rendah). Tingkat = Prob. × Dampak. Risiko tingkat Tinggi memerlukan pemantauan khusus setiap sprint.", 9, italic=True))

doc.save(DOCX_PATH)
print("✅ E. Manajemen Risiko selesai!")
