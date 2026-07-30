"""
Inject D.5 Software Effort Estimation into SPMP DOCX
"""
from docx import Document
from docx.shared import Pt
from docx.oxml.ns import nsdecls
from docx.oxml import parse_xml

DOCX_PATH = r"E:\UNIKOM\semester 6\Manajemen Proyek Perangkat Lunak\TUBES\SPMP - Sistem Informasi Manajemen Kost.docx"
doc = Document(DOCX_PATH)
body = doc.element.body

e_elem = None
for para in doc.paragraphs:
    if "E. MANAJEMEN RISIKO" in para.text:
        e_elem = para._element
        break

def esc(s):
    return s.replace("&","&amp;").replace("<","&lt;").replace(">","&gt;")

def P(text, size=11, bold=False, italic=False):
    p = f'<w:p {nsdecls("w")}><w:pPr><w:spacing w:after="120"/>'
    if bold: p += '<w:rPr><w:b/></w:rPr>'
    p += '</w:pPr>'
    p += f'<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="{size*2}"/>'
    if bold: p += '<w:b/>'
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
    # Header
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
            hal = 'left' if ci == 1 else 'center'
            tbl += f'<w:p><w:pPr><w:jc w:val="{hal}"/></w:pPr>'
            tbl += '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="16"/></w:rPr>'
            tbl += f'<w:t>{esc(str(val))}</w:t></w:r></w:p></w:tc>'
        tbl += '</w:tr>'
    tbl += '</w:tbl>'
    return parse_xml(tbl)

# ─── INSERT ALL ───
e_elem.addprevious(P("", 6))
e_elem.addprevious(H("D.5 Software Effort Estimation", 3))
e_elem.addprevious(P("Software Effort Estimation dilakukan untuk memperkirakan jumlah usaha (effort) dan waktu yang dibutuhkan dalam pengembangan sistem. Metode yang digunakan adalah Function Points (FP) yang dikembangkan oleh Allen Albrecht (1979) melalui tiga tahap: Total Unadjusted Function Points (TUFP), Processing Complexity (PC), dan Total Adjusted Function Points (TAFP).", 10))
e_elem.addprevious(H("Tabel D.5.1 Perhitungan TUFP", 4))

tufp_rows = [
    (["Inputs", "2", "3", "6", "6", "4", "24", "0", "6", "0", "30"], None),
    (["Outputs", "2", "4", "8", "4", "5", "20", "0", "7", "0", "28"], None),
    (["Queries", "2", "3", "6", "3", "4", "12", "0", "6", "0", "18"], None),
    (["Files", "1", "7", "7", "4", "10", "40", "0", "15", "0", "47"], None),
    (["Prog. Interfaces", "1", "5", "5", "0", "7", "0", "0", "10", "0", "5"], None),
    (["TOTAL", "", "", "", "", "", "", "", "", "", "128"], "E2EFDA"),
]
e_elem.addprevious(TBL(["Kategori","Low\n(Jml)","×Bobot","=Nilai","Medium\n(Jml)","×Bobot","=Nilai","High\n(Jml)","×Bobot","=Nilai","Total"],
    tufp_rows, [2.5,1.2,1.2,1.2,1.2,1.2,1.2,1.2,1.2,1.2,1.5]))

e_elem.addprevious(P("", 6))
e_elem.addprevious(H("Tabel D.5.2 Processing Complexity (PC)", 4))

pc_data = [
    ("Data Communications", "2", "Sistem berbasis web, data dikirim lewat internet"),
    ("Heavy Use Configuration", "1", "Konfigurasi standar Laragon / VPS"),
    ("Transaction Rate", "1", "Transaksi booking ~10-50 per hari"),
    ("End-User Efficiency", "2", "Antarmuka harus mudah digunakan"),
    ("Complex Processing", "2", "Validasi booking dan perhitungan tagihan"),
    ("Installation Ease", "1", "Deployment standar Laravel"),
    ("Multiple Sites", "0", "Hanya untuk satu lokasi kost"),
    ("Performance", "1", "Membutuhkan respons cukup cepat"),
    ("Distributed Functions", "2", "Dua sisi: Admin dan Pengguna"),
    ("On-line Data Entry", "3", "Banyak input data secara online"),
    ("On-line Update", "2", "Update status booking, bayar, aduan"),
    ("Reusability", "1", "Beberapa komponen dapat dipakai ulang"),
    ("Operational Ease", "2", "Admin harus mudah mengoperasikan"),
    ("Extensibility", "2", "Sistem dapat dikembangkan lebih lanjut"),
]
pc_rows = [(row, None) for row in pc_data]
pc_rows.append((["TOTAL PC", "22", ""], "E2EFDA"))
e_elem.addprevious(TBL(["Faktor", "Nilai", "Alasan"], pc_rows, [5.0, 1.5, 8.0]))

e_elem.addprevious(P("", 6))
e_elem.addprevious(H("Perhitungan TAFP, LOC, Effort, dan Time", 4))
e_elem.addprevious(P("PCA = 0,65 + (0,01 × 22) = 0,87\nTAFP = TUFP × PCA = 128 × 0,87 = 111,36 (dibulatkan 111)", 10))
e_elem.addprevious(P("Estimasi LOC menggunakan faktor konversi PHP/Laravel sebesar 55 LOC/FP [Capers Jones].\nLOC = TAFP × 55 = 111 × 55 = 6.125 baris kode", 10))
e_elem.addprevious(P("Estimasi effort menggunakan rumus COCOMO [Boehm, 1981]:\nEffort = 1,4 × (LOC / 1000) = 1,4 × 6,125 = 8,57 Person-Months", 10))
e_elem.addprevious(P("Estimasi waktu pengembangan:\nTime = 3,0 × (Effort)^(1/3) = 3,0 × 2,047 = 6,14 bulan", 10))

e_elem.addprevious(H("Tabel D.5.3 Perbandingan dengan Jadwal AON", 4))
comp_rows = [
    (["Total Function Points (TUFP)", "128", "—"], None),
    (["Total Adjusted FP (TAFP)", "111", "—"], None),
    (["Lines of Code (LOC)", "6.125", "—"], None),
    (["Effort (Person-Months)", "8,57", "2,37"], None),
    (["Estimasi Waktu (bulan)", "6,14", "3,87"], None),
]
e_elem.addprevious(TBL(["Metrik", "Estimasi FP", "AON Aktual"], comp_rows, [6.0, 3.0, 3.0]))

e_elem.addprevious(P("Catatan: Person-Months aktual dihitung dari total jam WBS (103 jam) dibagi 173 jam kerja per bulan. Tim terdiri dari 5 orang, sehingga effort FP perlu dibagi 5: 8,57/5 = 1,71 PM/orang. Perbedaan dengan AON (3,87 bulan) wajar karena estimasi FP menggunakan asumsi produktivitas standar industri, sedangkan AON menggunakan durasi kerja riil dengan paralelisasi tugas.", 9, italic=True))

doc.save(DOCX_PATH)
print("✅ D.5 Software Effort Estimation berhasil ditambahkan!")
