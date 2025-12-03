@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng')

@section('content')
<div class="container" style="padding: 50px 0;">
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
</script>
@endsection
