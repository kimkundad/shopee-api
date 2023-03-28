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
                                            <th class="p-0 ">status</th>
                                            <th class="p-0 "></th>
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
                                                    @if($item->active == 1)
                                                    checked="checked"
                                                    @endif
                                                    value="1"/>
                                                    <label class="form-check-label" for="allowmarketing"></label>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end flex-shrink-0">
                                                    <a href="{{url('admin/products/'.$item->id_q.'/edit')}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                        <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </a>
                                                    <a href="{{ url('api/del_pro/'.$item->id_q) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen027.svg-->
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </a>
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
        var user_id = $(this).closest('tr').attr('id');
    
        $.ajax({
                type:'POST',
                url:'{{url('api/api_add_product_shops')}}',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { "user_id" : user_id },
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
