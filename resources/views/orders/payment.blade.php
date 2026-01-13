@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng')

@section('content')
<div class="container" style="padding: 50px 0;">
    <style>
        .payment-success-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .payment-success-card {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 15px 45px rgba(0,0,0,0.18);
            max-width: 420px;
            text-align: center;
            animation: pop 0.25s ease-out;
        }
        .payment-success-check {
            width: 82px;
            height: 82px;
            margin: 0 auto 16px auto;
            border-radius: 50%;
            background: #e8f7ef;
            display: grid;
            place-items: center;
            color: #22c55e;
            font-size: 38px;
        }
        @keyframes pop {
            from { transform: scale(0.92); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>

    <div id="paymentSuccess" class="payment-success-overlay">
        <div class="payment-success-card">
            <div class="payment-success-check">✔</div>
            <h4 style="margin-bottom: 6px;">Thanh toán thành công</h4>
            <p class="text-muted mb-3">Đang chuyển bạn về trang Dashboard…</p>
            <div class="spinner-border text-success" role="status" style="width: 32px; height: 32px;"></div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">Thanh toán đơn hàng</h3>
                    <p class="mb-0 opacity-75">Mã đơn hàng: {{ $order->order_code }}</p>
                </div>
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <h4 class="text-muted mb-3">Tổng tiền cần thanh toán</h4>
                        <h1 class="text-primary font-weight-bold">{{ number_format($order->final_amount) }} VNĐ</h1>
                    </div>

                    <div class="row">
                        <div class="col-md-6 text-center mb-4 mb-md-0">
                            <h5 class="mb-3">Quét mã QR để thanh toán</h5>
                            <div class="qr-placeholder p-3 bg-light rounded d-inline-block">
                                <!-- Placeholder QR Code - Replace with real dynamic QR if needed -->
            <img src="https://img.vietqr.io/image/MB-99990123456969-compact2.png?amount={{ $order->final_amount }}&addInfo={{ $order->order_code }}&accountName=Le Thanh Phuc" 
         alt="QR Code Thanh Toán" 
         class="img-fluid"
         style="max-width: 100%; height: auto;">                            </div>
                            <p class="mt-2 text-muted small">Hỗ trợ: Momo, ZaloPay, Banking</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Thông tin chuyển khoản</h5>
                            <div class="bank-info p-3 bg-light rounded">
                                <div class="mb-3">
                                    <small class="text-muted d-block">Ngân hàng</small>
                                    <strong>MB Bank (Quân Đội)</strong>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Số tài khoản</small>
                                    <strong class="text-primary" style="font-size: 1.2em;">99990123456969</strong>
                                    <button class="btn btn-sm btn-link p-0 ml-2" onclick="copyToClipboard('99990123456969')"><i class="fas fa-copy"></i></button>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Chủ tài khoản</small>
                                    <strong>Le Thanh Phuc</strong>
                                </div>
                                <div class="mb-0">
                                    <small class="text-muted d-block">Nội dung chuyển khoản</small>
                                    <strong class="text-danger">{{ $order->order_code }}</strong>
                                    <button class="btn btn-sm btn-link p-0 ml-2" onclick="copyToClipboard('{{ $order->order_code }}')"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-5">

                    <div class="text-center">
                        <p class="mb-4">Sau khi chuyển khoản thành công, vui lòng nhấn nút bên dưới để xác nhận.</p>
                        <form action="{{ route('orders.confirm', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-check-circle mr-2"></i> Tôi đã chuyển khoản
                            </button>
                        </form>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-link text-muted mt-3">Để sau, quay về Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Đã sao chép: ' + text);
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}

// Poll order status so payment auto-completes after webhook
const statusUrl = "{{ url('/api/orders/'.$order->id.'/status') }}";
const successRedirect = "{{ route('user.dashboard') }}";
let paymentDone = false;

function pollOrderStatus() {
    if (paymentDone) return;
    fetch(statusUrl)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'paid' || data.payment_status === 'paid') {
                paymentDone = true;
                const overlay = document.getElementById('paymentSuccess');
                if (overlay) overlay.style.display = 'flex';
                setTimeout(() => {
                    window.location.href = successRedirect;
                }, 1500);
            }
        })
        .catch(err => console.error('Status check error', err));
}

setInterval(pollOrderStatus, 4000);// cứ 4 giây hỏi một lần
</script>
@endsection
