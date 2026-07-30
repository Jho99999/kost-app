<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; color: #374151; font-size: 14px; line-height: 1.6; margin: 0; }
.box { max-width: 520px; margin: 32px auto; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
.head { background: #d97706; padding: 20px 24px; color: #fff; }
.body { padding: 24px; }
.row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
.lbl { color: #6b7280; } .val { font-weight: 600; }
.cta { display: inline-block; margin-top: 20px; padding: 10px 24px; background: #1e3a5f; color: #fff; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; }
.footer { font-size: 12px; color: #9ca3af; margin-top: 16px; }
</style>
</head>
<body>
<div class="box">
  <div class="head"><strong>Pengingat Tagihan — 7 Hari Lagi</strong></div>
  <div class="body">
    <p>Halo <strong>{{ $payment->user->name }}</strong>, tagihan sewa kamar Anda akan jatuh tempo dalam <strong>7 hari</strong>. Segera lakukan pembayaran untuk menghindari status <em>overdue</em>.</p>

    <div class="row"><span class="lbl">Kode Tagihan</span><span class="val">{{ $payment->payment_code }}</span></div>
    <div class="row"><span class="lbl">Kamar</span><span class="val">{{ $payment->booking->room->name }}</span></div>
    <div class="row"><span class="lbl">Tagihan Bulan ke-</span><span class="val">{{ $payment->month_number }}</span></div>
    <div class="row"><span class="lbl">Nominal</span><span class="val">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></div>
    <div class="row"><span class="lbl">Jatuh Tempo</span><span class="val">{{ \Carbon\Carbon::parse($payment->due_date)->format('d M Y') }}</span></div>

    <p style="margin-top: 16px; font-size: 13px; color: #6b7280;">
      Setelah membayar, login ke aplikasi dan unggah bukti transfer pada halaman detail tagihan ini.
    </p>
    <p class="footer">Jika sudah melakukan pembayaran, abaikan email ini.</p>
  </div>
</div>
</body>
</html>
