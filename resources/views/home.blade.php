@extends('layouts.app')

@section('content')
<!-- Upload Form -->
<div class="row justify-content-center mb-5">
    <div class="col-lg-10">
        <div class="luxury-card p-5 animate-fadeInUp">
            <div class="text-center mb-4">
                <div class="bg-gradient-primary rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <span class="text-white fs-2">📁</span>
                </div>
                <h2 class="h3 fw-bold mb-3">رفع ملف Excel جديد</h2>
                <p class="text-muted">اختر ملفك وحدد البيانات المطلوبة بسهولة</p>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show animate-fadeInUp" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fs-4 me-3"></i>
                        <div>
                            <h6 class="mb-1">تم بنجاح!</h6>
                            <p class="mb-0">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show animate-fadeInUp" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
                        <div>
                            <h6 class="mb-2">حدث خطأ:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('excel.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- File Upload -->
                <div class="mb-4">
                    <label class="form-label fw-semibold fs-5">
                        <i class="fas fa-file-excel text-success me-2"></i>اختر ملف Excel
                    </label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls" class="form-control form-control-lg">
                    <div class="form-text">يدعم ملفات .xlsx و .xls حتى 10 ميجابايت</div>
                </div>

                <!-- Column Reference -->
                <div class="mb-4">
                    <label class="form-label fw-semibold fs-5">
                        <i class="fas fa-columns text-primary me-2"></i>رمز العمود
                    </label>
                    <input type="text" name="column_reference" value="{{ old('column_reference') }}" 
                           placeholder="مثال: A, B, C"
                           class="form-control form-control-lg">
                    <div class="form-text">أدخل رمز العمود المطلوب (مثل: A, B, C) - سيتم قراءة جميع البيانات في هذا العمود</div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-gradient btn-lg px-5 py-3 animate-pulse-glow">
                        <i class="fas fa-upload me-2"></i>رفع الملف وقراءة البيانات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Data Display Section -->
@if($excelData->count() > 0)
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="luxury-card p-5 animate-fadeInUp">
                <div class="text-center mb-4">
                    <div class="bg-gradient-success rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <span class="text-white fs-2">📊</span>
                    </div>
                    <h2 class="h3 fw-bold mb-3">البيانات المحفوظة</h2>
                    <p class="text-muted">{{ $excelData->count() }} ملف محفوظ</p>
                </div>
                
                <div class="row g-4">
                    @foreach($excelData as $index => $data)
                        <div class="col-12">
                            <div class="luxury-card p-4 animate-fadeInUp" style="animation-delay: {{ $index * 0.1 }}s">
                                <div class="row align-items-center">
                                    <div class="col-lg-8">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-3">
                                                <div class="text-center p-3 bg-light rounded-3">
                                                    <i class="fas fa-file-excel text-success fs-3 mb-2"></i>
                                                    <div class="fw-semibold text-muted small">اسم الملف</div>
                                                    <div class="fw-bold">{{ $data->file_name }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center p-3 bg-light rounded-3">
                                                    <i class="fas fa-table text-primary fs-3 mb-2"></i>
                                                    <div class="fw-semibold text-muted small">اسم الورقة</div>
                                                    <div class="fw-bold">{{ $data->sheet_name }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center p-3 bg-light rounded-3">
                                                    <i class="fas fa-columns text-warning fs-3 mb-2"></i>
                                                    <div class="fw-semibold text-muted small">رمز العمود</div>
                                                    <div class="fw-bold">{{ $data->cell_reference }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center p-3 bg-light rounded-3">
                                                    <i class="fas fa-clock text-info fs-3 mb-2"></i>
                                                    <div class="fw-semibold text-muted small">تاريخ الرفع</div>
                                                    <div class="fw-bold">{{ $data->created_at->format('Y-m-d H:i') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-gradient-primary text-white p-4 rounded-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="fas fa-gem fs-3 me-3"></i>
                                                <h5 class="mb-0">بيانات العمود {{ $data->cell_reference }}</h5>
                                            </div>
                                            <div class="bg-white text-dark p-3 rounded-2">
                                                @php
                                                    $columnData = json_decode($data->cell_value, true);
                                                @endphp
                                                @if($columnData && is_array($columnData))
                                                    <div class="row g-2">
                                                        @foreach($columnData as $item)
                                                            <div class="col-md-6 col-lg-4">
                                                                <div class="p-2 rounded text-center {{ isset($item['is_duplicate']) && $item['is_duplicate'] ? 'bg-danger text-white' : 'bg-light' }}">
                                                                    <small class="{{ isset($item['is_duplicate']) && $item['is_duplicate'] ? 'text-white-50' : 'text-muted' }}">الصف {{ $item['row'] }}</small>
                                                                    <div class="fw-bold">{{ $item['value'] }}</div>
                                                                    @if(isset($item['is_duplicate']) && $item['is_duplicate'])
                                                                        <small class="text-white-50 d-block mt-1">
                                                                            <i class="fas fa-exclamation-triangle me-1"></i>مكرر
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="mb-0 text-center">{{ $data->cell_value }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4 text-center">
                                        <form action="{{ route('excel.delete', $data->id) }}" method="POST" 
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذه البيانات؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-gradient-danger btn-lg">
                                                <i class="fas fa-trash me-2"></i>حذف
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="luxury-card p-5 text-center animate-fadeInUp">
                <div class="icon-large animate-float">📊</div>
                <h3 class="h2 fw-bold mb-3">لا توجد بيانات محفوظة</h3>
                <p class="lead text-muted mb-4">ارفع ملف Excel أولاً لرؤية البيانات هنا</p>
                <div class="d-inline-flex align-items-center bg-light px-4 py-3 rounded-pill">
                    <div class="bg-primary rounded-circle me-3 animate-pulse" style="width: 12px; height: 12px;"></div>
                    <span class="fw-semibold">جاهز لاستقبال ملفاتك</span>
            </div>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // إضافة تأثيرات بصرية للعناصر
    const cards = document.querySelectorAll('.luxury-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.2}s`;
    });
});
</script>
@endsection
