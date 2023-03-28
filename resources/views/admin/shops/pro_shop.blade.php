@extends('admin.layouts.template')

@section('title')
    <title>shopee-app</title>
    <meta name="description" content="shopee-app">
@stop
@section('stylesheet')

@stop('stylesheet')

@section('content')

    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            แก้ไขสินค้าในร้าน</h1>
                        <!--end::Title-->
                        
                    </div>
                    <!--end::Page title-->
                    <a href="{{ url('admin/shops/'.$shop_id.'/edit') }}" class="btn btn-sm fw-bold btn-primary" >
                        <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/kt-products/docs/metronic/html/releases/2023-03-24-172858/core/html/src/media/icons/duotune/arrows/arr054.svg-->
                        <span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.7189 13.9C17.6189 9.8 11.4189 8.79999 6.21895 11.2L7.31895 12.9C11.719 11 16.919 12 20.319 15.4C20.519 15.6 20.819 15.7 21.019 15.7C21.219 15.7 21.5189 15.6 21.7189 15.4C22.1189 14.9 22.1189 14.2 21.7189 13.9Z" fill="currentColor"/>
                            <path opacity="0.3" d="M10.119 17.1L3.61896 7L2.01895 14.3C1.91895 14.8 2.21895 15.4 2.81895 15.5L10.119 17.1Z" fill="currentColor"/>
                            </svg>
                            </span>
                            <!--end::Svg Icon-->
                        กลับไปยังร้านค้า
                    </a>
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">จำนวนสินค้าทั้งหมด</span>
                                <span class="text-muted mt-1 fw-semibold fs-7"> จำนวน {{ count($objs) }} รายการ</span>
                            </h3>
                        </div>
                        <div class="card-body py-3">

                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table align-middle gs-0 gy-3">
                                    <!--begin::Table head-->
                                    <thead>
                                        <tr>
                                            <th class="p-0 w-50px"></th>
                                            <th class="p-0 "></th>
                                            <th class="p-0 ">หมู่สินค้า</th>
                                            <th class="p-0 ">บัญชี</th>
                                            <th class="p-0 ">ราคา</th>
                                            <th class="p-0 ">จำนวนสินค้า</th>
                                            <th class="p-0 ">ดึงสินค้า</th>
                                       
                                        </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>
                                        @isset($objs)
                                            @foreach ($objs as $item)
                                        

                                        <tr id="{{$item->id_q}}">
                                            <td>
                                                <div class="symbol symbol-50px">
                                                    <img src="{{ url('images/shopee/products/'.$item->img_product) }}" alt="">
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $item->name_product }}</a>
                                                <span class="text-muted fw-semibold d-block fs-7">SKU {{ $item->sku }}</span>
                                            </td>
                                            <td>
                                                {{ ($item->cat_name) }}
                                            </td>
                                            <td>
                                                {{ $item->fname }} {{ $item->lname }}
                                            </td>
                                            <td>
                                                {{ number_format($item->price,2) }}
                                            </td>
                                            <td>
                                                {{ ($item->stock) }} / ชิ้น
                                            </td>
                                            <td>
                                                <div class="form-check form-check-solid form-switch form-check-custom fv-row">
                                                    <input class="form-check-input w-45px h-30px" type="checkbox" id="allowmarketing" name="status" 
                                                    value="1"/>
                                                    <label class="form-check-label" for="allowmarketing"></label>
                                                </div>
                                            </td>
                                            
                                        </tr>

                                            @endforeach

                                        @endisset
                                       
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                            </div>
                            @if(count($objs) > 10)
                            @include('admin.pagination.default', ['paginator' => $objs])
                            @endif
                        </div>
                    </div>
                    
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
        <!--begin::Footer-->
        <div id="kt_app_footer" class="app-footer">
            <!--begin::Footer container-->
            <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
                <!--begin::Copyright-->
                <div class="text-dark order-2 order-md-1">
                    <span class="text-muted fw-semibold me-1">2022&copy;</span>
                    <a href="" target="_blank" class="text-gray-800 text-hover-primary">shopee-app</a>
                </div>
                <!--end::Copyright-->
                <!--begin::Menu-->
                
                <!--end::Menu-->
            </div>
            <!--end::Footer container-->
        </div>
        <!--end::Footer-->
    </div>

@endsection

@section('scripts')


<script type="text/javascript">
    $(document).ready(function(){
      $("input:checkbox").change(function() {
        var pro_id = $(this).closest('tr').attr('id');
    
        $.ajax({
                type:'POST',
                url:'{{url('api/api_add_product_shops')}}',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { "pro_id" : pro_id, "shop_id" : {{ $shop_id }}},
                success: function(data){
                  if(data.data.success){
    
    
                    Swal.fire({
                        text: "ระบบได้ทำการอัพเดทข้อมูลสำเร็จ!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
    
    
    
                  }
                }
            });
        });
    });
    </script>

@stop('scripts')
