<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; color: #374151; font-size: 14px; line-height: 1.6; margin: 0; }
.box { max-width: 520px; margin: 32px auto; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
.head { background: #dc2626; padding: 20px 24px; color: #fff; }
.body { padding: 24px; }
.row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
.lbl { color: #6b7280; } .val { font-weight: 600; }
.note { background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 12px; margin-top: 16px; font-size: 13px; color: #991b1b; }
.footer { font-size: 12px; color: #9ca3af; margin-top: 20px; }
</style>
</head>
<body>
<div class="box">
  <div class="head"><strong>Pemesanan Tidak Dapat Disetujui</strong></div>
  <div class="body">
    <p>Halo <strong>{{ $booking->user->name }}</strong>, maaf pengajuan pemesanan kamar Anda tidak dapat disetujui saat ini.</p>
    <div class="row"><span class="lbl">Kode Booking</span><span class="val">{{ $booking->booking_code }}</span></div>
    <div class="row"><span class="lbl">Kamar</span><span class="val">{{ $booking->room->name }}</span></div>
    <div class="row"><span class="lbl">Tanggal Pengajuan</span><span class="val">{{ $booking->created_at->format('d M Y') }}</span></div>

    @if($booking->notes)
    <div class="note">
      <strong>Catatan dari admin:</strong><br>
      {{ $booking->notes }}
    </div>
    @endif

    <p class="footer">
      Anda dapat mengajukan pemesanan baru untuk kamar lain, atau menghubungi admin secara langsung untuk informasi lebih lanjut.
    </p>
  </div>
</div>
</body>
</html>
