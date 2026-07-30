<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; color: #374151; font-size: 14px; line-height: 1.6; margin: 0; padding: 0; }
.box { max-width: 520px; margin: 32px auto; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
.head { background: #1e3a5f; padding: 20px 24px; color: #fff; }
.body { padding: 24px; }
.row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
.lbl { color: #6b7280; } .val { font-weight: 600; }
.footer { font-size: 12px; color: #9ca3af; margin-top: 20px; }
</style>
</head>
<body>
<div class="box">
  <div class="head"><strong>Pengajuan Pemesanan Baru</strong></div>
  <div class="body">
    <p>Ada pengajuan pemesanan kamar baru yang menunggu persetujuan Anda.</p>
    <div class="row"><span class="lbl">Kode Booking</span><span class="val">{{ $booking->booking_code }}</span></div>
    <div class="row"><span class="lbl">Penyewa</span><span class="val">{{ $booking->user->name }}</span></div>
    <div class="row"><span class="lbl">Email Penyewa</span><span class="val">{{ $booking->user->email }}</span></div>
    <div class="row"><span class="lbl">Kamar</span><span class="val">{{ $booking->room->name }} ({{ $booking->room->type }})</span></div>
    <div class="row"><span class="lbl">Tanggal Masuk</span><span class="val">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</span></div>
    <div class="row"><span class="lbl">Durasi</span><span class="val">{{ $booking->duration_months }} bulan</span></div>
    <div class="row"><span class="lbl">Total Sewa</span><span class="val">Rp {{ number_format($booking->room->price * $booking->duration_months, 0, ',', '.') }}</span></div>
    <p class="footer">Silakan masuk ke dashboard admin untuk menyetujui atau menolak pemesanan ini.</p>
  </div>
</div>
</body>
</html>
