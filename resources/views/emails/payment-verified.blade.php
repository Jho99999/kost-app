<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; color: #374151; font-size: 14px; line-height: 1.6; margin: 0; }
.box { max-width: 520px; margin: 32px auto; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
.head { background: #16a34a; padding: 20px 24px; color: #fff; }
.body { padding: 24px; }
.row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
.lbl { color: #6b7280; } .val { font-weight: 600; }
.badge { display: inline-block; background: #dcfce7; color: #15803d; border-radius: 9999px; padding: 4px 14px; font-size: 13px; font-weight: 600; margin-bottom: 16px; }
.footer { font-size: 12px; color: #9ca3af; margin-top: 20px; }
</style>
</head>
<body>
<div class="box">
  <div class="head"><strong>Pembayaran Dikonfirmasi</strong></div>
  <div class="body">
    <span class="badge">LUNAS</span>
    <p>Halo <strong>{{ $payment->user->name }}</strong>, pembayaran sewa kamar Anda bulan ke-{{ $payment->month_number }} telah dikonfirmasi oleh admin.</p>

    <div class="row"><span class="lbl">Kode Tagihan</span><span class="val">{{ $payment->payment_code }}</span></div>
    <div class="row"><span class="lbl">Kamar</span><span class="val">{{ $payment->booking->room->name }}</span></div>
    <div class="row"><span class="lbl">Tagihan Bulan ke-</span><span class="val">{{ $payment->month_number }}</span></div>
    <div class="row"><span class="lbl">Nominal</span><span class="val">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></div>
    <div class="row"><span class="lbl">Tanggal Dikonfirmasi</span><span class="val">{{ now()->format('d M Y, H:i') }} WIB</span></div>

    <p class="footer">Terima kasih atas pembayaran tepat waktu. Simpan email ini sebagai bukti konfirmasi.</p>
  </div>
</div>
</body>
</html>
