"""
Full rewrite of D.5 Software Effort Estimation with all corrections
"""
from docx import Document
from docx.shared import Pt
from docx.oxml.ns import nsdecls
from docx.oxml import parse_xml

DOCX_PATH = r"E:\UNIKOM\semester 6\Manajemen Proyek Perangkat Lunak\TUBES\SPMP - Sistem Informasi Manajemen Kost.docx"
doc = Document(DOCX_PATH)
body = doc.element.body

# ─── FIND SECTION BOUNDARIES ───
d5_elem = None
e_elem = None
for para in doc.paragraphs:
    if "D.5 Software Effort Estimation" in para.text:
        d5_elem = para._element
    if "E. MANAJEMEN RISIKO" in para.text:
        e_elem = para._element
        break

# ─── REMOVE OLD D.5 CONTENT ───
if d5_elem and e_elem:
    children = list(body)
    in_range = False
    to_remove = []
    for child in children:
        if child is d5_elem:
            in_range = True
        if child is e_elem:
            break
        if in_range:
            to_remove.append(child)
    
    for elem in to_remove:
        try:
            body.remove(elem)
        except:
            pass
    print(f"✅ Removed {len(to_remove)} old D.5 elements")
else:
    print("⚠️  Could not find D.5 or E section")

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

def TBL(headers, rows, col_cm=None, first_col_align='center'):
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
            hal = 'left' if ci == 2 else first_col_align if ci == 0 else 'center'
            tbl += f'<w:p><w:pPr><w:jc w:val="{hal}"/></w:pPr>'
            tbl += '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="16"/></w:rPr>'
            tbl += f'<w:t>{esc(str(val))}</w:t></w:r></w:p></w:tc>'
        tbl += '</w:tr>'
    tbl += '</w:tbl>'
    return parse_xml(tbl)

# ─── NEW VALUES ───
# TUFP: Program Interfaces = 0
tufp = 30 + 28 + 18 + 47 + 0  # = 123
# PC: Distributed Functions = 0
pc = 2+1+1+2+2+1+0+1+0+3+2+1+2+2  # = 20
pca = 0.65 + 0.01 * pc  # = 0.85
tafp_unrounded = tufp * pca  # = 104.55
tafp_rounded = round(tafp_unrounded)  # = 105
loc_fp = 55
loc = tafp_unrounded * loc_fp  # = 5750.25
effort = 1.4 * (loc / 1000)  # = 8.05
time_m = 3.0 * (effort ** (1/3))

# Print for verification
print(f"TUFP = {tufp}")
print(f"PC = {pc}")
print(f"PCA = {pca}")
print(f"TAFP = {tafp_unrounded:.2f} (rounded: {tafp_rounded})")
print(f"LOC = {loc:.0f}")
print(f"Effort = {effort:.2f} PM")
print(f"Time = {time_m:.2f} bulan")

# ─── INSERT NEW D.5 CONTENT (BEFORE E) ───
def ins(elem):
    e_elem.addprevious(elem)

# Spacing
ins(P("", 6))

# Heading
ins(H("D.5 Software Effort Estimation", 3))

# Introduction
ins(P("Software Effort Estimation dilakukan untuk memperkirakan jumlah usaha (effort) dan waktu yang dibutuhkan dalam pengembangan Sistem Informasi Manajemen Kost. Metode yang digunakan adalah Function Points (FP) yang dikembangkan oleh Allen Albrecht (1979) melalui tiga tahap: Total Unadjusted Function Points (TUFP), Processing Complexity (PC), dan Total Adjusted Function Points (TAFP).", 10))

# ─── FUNCTION MAPPING TABLE (NEW!) ───
ins(H("Tabel D.5.1 Pemetaan Fungsi Sistem ke Kategori FP", 4))
ins(P("Tabel berikut menunjukkan fungsi-fungsi sistem yang diidentifikasi dan dikelompokkan ke dalam lima kategori Function Points beserta kompleksitas dan alasan penetapannya.", 9))

func_rows = [
    (["Input", "Registrasi pengguna", "Low", "Memasukkan data login dan profil"], None),
    (["Input", "Pengelolaan kamar", "Medium", "Menambah/mengubah data kamar (nama, type, harga, fasilitas)"], None),
    (["Input", "Pengajuan booking", "Medium", "Memilih kamar, mengisi durasi, submit booking"], None),
    (["Input", "Upload bukti pembayaran", "Medium", "Memasukkan data pembayaran + upload file gambar"], None),
    (["Input", "Pengajuan aduan", "Medium", "Input deskripsi kerusakan + upload foto"], None),
    (["Input", "Verifikasi pembayaran", "Medium", "Memeriksa bukti dan mengubah status pembayaran"], None),
    (["Input", "Tanggapan aduan", "Low", "Memasukkan tanggapan admin terhadap aduan"], None),
    (["Input", "Check-out / perpanjangan", "Medium", "Memproses check-out atau perpanjangan sewa"], None),
    (["Output", "Dashboard admin", "Medium", "Menampilkan ringkasan statistik dan grafik"], None),
    (["Output", "Detail kamar", "Low", "Menampilkan informasi kamar dan fasilitas"], None),
    (["Output", "Riwayat booking", "Medium", "Menampilkan daftar booking beserta statusnya"], None),
    (["Output", "Tagihan pembayaran", "Medium", "Menampilkan rincian tagihan per periode"], None),
    (["Output", "Status aduan", "Low", "Menampilkan status penanganan aduan"], None),
    (["Output", "Laporan penghuni", "Medium", "Menampilkan rekap data penghuni per kamar"], None),
    (["Query", "Pencarian kamar", "Low", "Mencari kamar berdasarkan type/status/harga"], None),
    (["Query", "Filter booking", "Medium", "Memfilter booking berdasarkan status/tanggal"], None),
    (["Query", "Filter pembayaran", "Medium", "Memfilter pembayaran berdasarkan status"], None),
    (["Query", "Cek ketersediaan kamar", "Low", "Melihat status available/occupied kamar"], None),
    (["Query", "Cari aduan", "Medium", "Memfilter aduan berdasarkan status/kamar"], None),
    (["File", "Users", "Low", "Data profil pengguna (penghuni dan admin)"], None),
    (["File", "Rooms", "Medium", "Data kamar, harga, fasilitas, dan status"], None),
    (["File", "Bookings", "Medium", "Data transaksi sewa dan statusnya"], None),
    (["File", "Payments", "Medium", "Data riwayat pembayaran per bulan"], None),
    (["File", "Complaints", "Medium", "Data aduan kerusakan dan penanganannya"], None),
    (["Interface", "—", "—", "Tidak ada integrasi dengan sistem eksternal"], "FFEBEE"),
]

ins(TBL(["Kategori", "Fungsi Sistem", "Kompleksitas", "Alasan Penetapan"],
    func_rows, [2.0, 4.5, 2.0, 6.0], first_col_align='center'))

ins(P("", 6))

# ─── TABLE 2: TUFP Calculation ───
ins(H("Tabel D.5.2 Perhitungan TUFP (Total Unadjusted Function Points)", 4))

tufp_rows = [
    (["Inputs", "8", "2", "3", "6", "6", "4", "24", "0", "6", "0", "30"], None),
    (["Outputs", "6", "2", "4", "8", "4", "5", "20", "0", "7", "0", "28"], None),
    (["Queries", "5", "2", "3", "6", "3", "4", "12", "0", "6", "0", "18"], None),
    (["Files", "5", "1", "7", "7", "4", "10", "40", "0", "15", "0", "47"], None),
    (["Prog. Interfaces", "0", "0", "5", "0", "0", "7", "0", "0", "10", "0", "0"], None),
    (["TOTAL", "", "", "", "", "", "", "", "", "", "", "123"], "E2EFDA"),
]

ins(TBL(["Kategori", "Jumlah\nFungsi", "Low\n(Jml)", "×Bobot", "=Nilai",
         "Medium\n(Jml)", "×Bobot", "=Nilai", "High\n(Jml)", "×Bobot", "=Nilai", "Total"],
    tufp_rows, [2.5, 1.0, 1.0, 1.0, 1.0, 1.2, 1.0, 1.0, 1.0, 1.0, 1.0, 1.2]))

ins(P("", 6))

# ─── TABLE 3: PC ───
ins(H("Tabel D.5.3 Processing Complexity (PC)", 4))

pc_data = [
    ("Data Communications", "2", "Sistem berbasis web, data dikirim melalui internet"),
    ("Heavy Use Configuration", "1", "Konfigurasi server standar (VPS)"),
    ("Transaction Rate", "1", "Frekuensi transaksi rendah (~10–50 per hari)"),
    ("End-User Efficiency", "2", "Antarmuka harus mudah digunakan oleh admin dan penghuni"),
    ("Complex Processing", "2", "Validasi booking dan perhitungan tagihan otomatis"),
    ("Installation Ease", "1", "Deployment menggunakan Laravel standar"),
    ("Multiple Sites", "0", "Hanya melayani satu lokasi kost"),
    ("Performance", "1", "Membutuhkan respons yang cukup cepat"),
    ("Distributed Functions", "0", "Seluruh proses berjalan pada satu server dan satu database"),
    ("On-line Data Entry", "3", "Banyak input data dilakukan secara online"),
    ("On-line Update", "2", "Status booking, pembayaran, dan aduan diupdate secara online"),
    ("Reusability", "1", "Beberapa komponen (CRUD, auth) dapat digunakan kembali"),
    ("Operational Ease", "2", "Admin harus mudah dalam operasional sehari-hari"),
    ("Extensibility", "2", "Sistem dapat dikembangkan lebih lanjut"),
]

pc_rows = [(row, None) for row in pc_data]
pc_rows.append((["TOTAL PC", "20", ""], "E2EFDA"))

ins(TBL(["Faktor", "Nilai", "Alasan"], pc_rows, [5.0, 1.5, 8.0]))
ins(P("", 6))

# ─── CALCULATIONS ───
ins(H("Perhitungan TAFP, LOC, Effort, dan Time", 4))

ins(P("1) Total Adjusted Function Points (TAFP)", 10, bold=True))
ins(P(f"PCA = 0,65 + (0,01 × PC) = 0,65 + (0,01 × {pc}) = {pca}", 10))
ins(P(f"TAFP = TUFP × PCA = {tufp} × {pca} = {tafp_unrounded:.2f} (dibulatkan menjadi {tafp_rounded} FP)", 10))

ins(P("2) Estimasi Lines of Code (LOC)", 10, bold=True))
ins(P(f"Menggunakan asumsi 55 LOC per FP (berdasarkan referensi Capers Jones untuk bahasa Java, digunakan sebagai pendekatan untuk PHP/Laravel):", 9, italic=True))
ins(P(f"LOC = TAFP × 55 = {tafp_unrounded:.2f} × 55 = {loc:.0f} baris kode", 10))

ins(P("3) Estimasi Effort", 10, bold=True))
ins(P(f"Menggunakan rumus COCOMO Basic [Boehm, 1981] untuk organic mode:", 10))
ins(P(f"Effort = 1,4 × (LOC / 1000) = 1,4 × {loc/1000:.3f} = {effort:.2f} Person-Months", 10))

ins(P("4) Estimasi Waktu Pengembangan", 10, bold=True))
ins(P(f"Time = 3,0 × (Effort)^(1/3) = 3,0 × ({effort:.2f})^(1/3)", 10))
ins(P(f"Time = 3,0 × {effort**(1/3):.3f} = {time_m:.2f} bulan ≈ {time_m*22:.0f} hari kerja", 10))

ins(P("5) Interpretasi", 10, bold=True))
ins(P(f"Estimasi ini mengasumsikan pengembangan oleh satu orang. Dengan tim proyek yang terdiri dari 5 orang, effort per orang secara teoritis adalah {effort/5:.2f} Person-Months. Namun dalam praktiknya, pembagian tugas tidak merata dan terdapat ketergantungan antar tugas (sebagaimana digambarkan dalam diagram AON), sehingga waktu aktual (84 hari kerja) lebih pendek dari estimasi teoritis ini.", 10))

# ─── SUMMARY TABLE ───
ins(H("Tabel D.5.4 Ringkasan Hasil Estimasi", 4))

summary_rows = [
    (["Total Unadjusted Function Points (TUFP)", str(tufp)], None),
    (["Processing Complexity (PC)", str(pc)], None),
    (["Adjusted Processing Complexity (PCA)", f"{pca:.2f}"], None),
    (["Total Adjusted Function Points (TAFP)", f"{tafp_rounded} ({tafp_unrounded:.2f})"], None),
    (["Estimasi LOC (55 LOC/FP)", f"{loc:.0f} baris"], None),
    (["Estimasi Effort", f"{effort:.2f} Person-Months"], None),
    (["Estimasi Waktu", f"{time_m:.2f} bulan ({time_m*22:.0f} hari kerja)"], None),
]
ins(TBL(["Metrik", "Nilai"], summary_rows, [8.0, 5.0]))
ins(P("", 6))

doc.save(DOCX_PATH)
print("✅ D.5 section rebuilt with all corrections!")
