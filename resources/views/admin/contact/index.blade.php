@extends('admin.layouts.template')

@section('title')
    <title>วงษ์พาณิชย์รีไซเคิล ระยอง จำกัด</title>
    <meta name="description" content=" รายละเอียด วงษ์พาณิชย์รีไซเคิล ระยอง จำกัด">
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
                            ข้อมูลติดต่อเราทั้งหมด</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ url('dashboard') }}" class="text-muted text-hover-primary">จัดการ</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">ข้อมูลติดต่อเรา</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
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
                        
                        <div class="card-body py-3">

                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table align-middle gs-0 gy-3">
                                    <!--begin::Table head-->
                                    <thead>
                                        <tr>
                                            <th class="p-0 w-50px"></th>
                                            <th class="p-0 "></th>
                                            <th class="p-0 "></th>
                                            <th class="p-0 ">หัวข้อติดต่อ</th>
                                            <th class="p-0 ">อ่านแล้ว</th>
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
                                                <div class="symbol symbol-50px me-5">
                                                    <span class="symbol-label bg-light-primary">
                                                        <!--begin::Svg Icon | path: icons/duotune/communication/com012.svg-->
                                                        <span class="svg-icon svg-icon-2x svg-icon-primary">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"></path>
                                                                <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"></rect>
                                                                <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <a href="#" class="text-dark text-hover-primary fs-6 fw-bold">{{ $item->names }}</a>
                                                    <a href="#" class="text-muted text-hover-primary fw-semibold text-muted d-block fs-7">
                                                        <span class="text-dark">Email</span>: {{ $item->email }}</a>
                                                </div>
                                               
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    
                                                    <a href="#" class="text-muted text-hover-primary fw-semibold text-muted d-block fs-7">
                                                        <span class="text-dark">Phone </span>: {{ $item->phone }}</a>
                                                </div>
                                               
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    
                                                    <a href="#" class="text-muted text-hover-primary fw-semibold text-muted d-block fs-7">
                                                        <span class="text-dark">Phone </span>: {{ $item->name1 }}</a>
                                                </div>
                                               
                                            </td>
                                            <td>
                                                <div class="form-check form-check-solid form-switch form-check-custom fv-row">
                                                    <input class="form-check-input w-45px h-30px" type="checkbox" id="allowmarketing" name="status" 
                                                    @if($item->status1 == 1)
                                                    checked="checked"
                                                    @endif
                                                    value="1"/>
                                                    <label class="form-check-label" for="allowmarketing"></label>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end flex-shrink-0">
                                                    <a href="#" class="btn btn-icon btn-light btn-sm border-0" data-bs-toggle="modal" data-bs-target="#kt_modal_{{$item->id_q}}">
                                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                                        <span class="svg-icon svg-icon-2 svg-icon-primary">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>


                                        <div class="modal fade" tabindex="-1" id="kt_modal_{{$item->id_q}}">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title">{{ $item->names }}</h3>
                                        
                                                        <!--begin::Close-->
                                                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                                            <span class="svg-icon svg-icon-1"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"></rect>
                                                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"></rect>
                                                            </svg>
                                                            
                                                            </span>
                                                            <!--end::Svg Icon-->                            
                                                        </div>
                                                        <!--end::Close-->
                                                    </div>
                                        
                                                    <div class="modal-body">
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <a href="#" class="text-dark fw-bold text-hover-primary fs-6">ชื่อ-นามสกุล</a>
                                                            <span class="text-muted fw-semibold text-muted d-block fs-7">{{ $item->names }}</span>
                                                        </div>
                                                        <hr>
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <a href="#" class="text-dark fw-bold text-hover-primary fs-6">อีเมล</a>
                                                            <span class="text-muted fw-semibold text-muted d-block fs-7">{{ $item->email }}</span>
                                                        </div><hr>
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <a href="#" class="text-dark fw-bold text-hover-primary fs-6">เบอร์โทรศัพท์</a>
                                                            <span class="text-muted fw-semibold text-muted d-block fs-7">{{ $item->phone }}</span>
                                                        </div><hr>
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <a href="#" class="text-dark fw-bold text-hover-primary fs-6">หัวข้อที่ต้องการติดต่อ</a>
                                                            <span class="text-muted fw-semibold text-muted d-block fs-7">{{ $item->name1 }}</span>
                                                        </div><hr>
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <a href="#" class="text-dark fw-bold text-hover-primary fs-6">ข้อความ</a>
                                                            <span class="text-muted fw-semibold text-muted d-block fs-7">{{ $item->messenger }}</span>
                                                        </div>
                                                    </div>
                                        
                                                    <div class="modal-footer">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

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
                    <a href="" target="_blank" class="text-gray-800 text-hover-primary">บริษัท วงษ์พาณิชย์รีไซเคิล ระยอง จำกัด</a>
                </div>
                <!--end::Copyright-->
                <!--begin::Menu-->
                <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
                    <li class="menu-item">
                        <a href="{{ url('about') }}" target="_blank" class="menu-link px-2">เกี่ยวกับวงษ์พาณิชย์</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ url('contatermct') }}" target="_blank" class="menu-link px-2">นโยบายส่วนบุคคล</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ url('contact') }}" target="_blank" class="menu-link px-2">ติดต่อเรา</a>
                    </li>
                </ul>
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
                url:'{{url('api/api_post_status_contact')}}',
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
