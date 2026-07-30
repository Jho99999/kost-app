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
table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 13px; }
th { background: #f0fdf4; padding: 8px 10px; text-align: left; border: 1px solid #bbf7d0; }
td { padding: 8px 10px; border: 1px solid #e5e7eb; }
.footer { font-size: 12px; color: #9ca3af; margin-top: 20px; }
</style>
</head>
<body>
<div class="box">
  <div class="head"><strong>Pemesanan Anda Disetujui</strong></div>
  <div class="body">
    <p>Selamat, <strong>{{ $booking->user->name }}</strong>! Admin telah menyetujui pengajuan pemesanan kamar Anda.</p>
    <div class="row"><span class="lbl">Kode Booking</span><span class="val">{{ $booking->booking_code }}</span></div>
    <div class="row"><span class="lbl">Kamar</span><span class="val">{{ $booking->room->name }} ({{ $booking->room->type }})</span></div>
    <div class="row"><span class="lbl">Tanggal Masuk</span><span class="val">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</span></div>
    <div class="row"><span class="lbl">Durasi</span><span class="val">{{ $booking->duration_months }} bulan</span></div>

    <h4 style="margin: 20px 0 8px; font-size: 14px;">Jadwal Tagihan Bulanan</h4>
    <table>
      <thead>
        <tr><th>Bulan ke-</th><th>Jatuh Tempo</th><th>Nominal</th></tr>
      </thead>
      <tbody>
        @foreach($booking->payments as $payment)
        <tr>
          <td style="text-align:center">{{ $payment->month_number }}</td>
          <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('d M Y') }}</td>
          <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <p class="footer">
      Login ke aplikasi untuk melihat detail tagihan dan mengunggah bukti pembayaran sebelum tanggal jatuh tempo.
    </p>
  </div>
</div>
</body>
</html>
