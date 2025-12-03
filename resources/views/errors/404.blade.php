{{-- resources/views/errors/404.blade.php --}}
@extends('layouts.app') 

@section('title', '404 - Không tìm thấy')

@section('content')
<section class="error-section">
    <div class="container error-container">
        <div class="error-content">
            
            <div class="error-visual">
                <div class="error-code" data-text="404">404</div>
            </div>

            <h2 class="error-title">Lạc Lối Giữa Không Gian Sự Kiện!</h2>
            
            <p class="error-message">
                Chúng tôi xin lỗi, trang bạn đang tìm kiếm dường như đã được chuyển tới một vũ trụ song song, hoặc đơn giản là không tồn tại.
            </p>

            <a href="{{ route('home') }}" class="btn btn-primary btn-lg error-btn">
                <i class="fas fa-home"></i> Trở về hành tinh mẹ
            </a>

        </div>
    </div>
</section>
@endsection

@push('styles')
    <style>
        /* CSS cho trang 404 mới */
        .error-section {
            /* BUỘC CĂN GIỮA VÀ CHIẾM TOÀN BỘ CHIỀU RỘNG/CHIỀU CAO */
            min-height: 80vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important; /* <-- BUỘC CĂN GIỮA NỘI DUNG */
            text-align: center !important; /* <-- BUỘC CĂN GIỮA CHỮ */
            background: #f8f9fa !important; 
            padding: 60px 0 !important;
            width: 100% !important; /* Đảm bảo nó chiếm 100% */
        }

        .error-content {
            max-width: 600px !important;
            padding: 40px !important;
            border-radius: 15px !important;
            background: #fff !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            animation: fadeIn 1s ease-out;
            /* Đảm bảo nội dung bên trong được căn giữa hoàn toàn */
            margin: 0 auto !important; 
            box-sizing: border-box !important;
        }

        /* Hiệu ứng 404 nổi bật */
        .error-visual {
            position: relative;
            margin-bottom: 20px;
        }

        .error-code {
            font-size: 150px !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            color: transparent !important; 
            -webkit-text-stroke: 2px var(--primary-color, #6c5ce7) !important;
            text-stroke: 2px var(--primary-color, #6c5ce7) !important;
            position: relative;
            display: inline-block !important;
            transition: transform 0.3s ease;
        }
        
        /* Hiệu ứng 3D nhẹ khi hover */
        .error-code:hover {
            transform: scale(1.05) rotateZ(-1deg);
        }

        /* Lớp phủ (fill) bên trong chữ 404 */
        .error-code::before {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 0%;
            height: 100%;
            overflow: hidden;
            color: var(--primary-color, #6c5ce7) !important; 
            transition: width 1s ease;
            -webkit-text-stroke: 0 !important;
            text-stroke: 0 !important;
            animation: fillUp 3s ease-out forwards;
        }

        @keyframes fillUp {
            0% { width: 0%; }
            50% { width: 80%; }
            100% { width: 100%; }
        }

        .error-title {
            font-size: 30px !important;
            color: #333 !important;
            margin-bottom: 15px !important;
        }

        .error-message {
            color: #777 !important;
            margin-bottom: 30px !important;
        }

        .error-btn {
            background-color: var(--primary-color, #6c5ce7) !important;
            border-color: var(--primary-color, #6c5ce7) !important;
            transition: background-color 0.3s, transform 0.3s;
        }
        
        /* Hiệu ứng nút khi hover */
        .error-btn:hover {
            background-color: var(--accent-color, #a29bfe) !important;
            transform: translateY(-2px);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush