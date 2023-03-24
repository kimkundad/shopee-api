@extends('admin.layouts.template')

@section('title')
    <title>shopee-app</title>
    <meta name="description" content=" shopee-app">
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
                            แก้ไขข้อมูลผู้เปิดร้านค้า </h1>
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
                            <li class="breadcrumb-item text-muted">แก้ไขข้อมูลผู้เปิดร้านค้า</li>
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
                    <form id="kt_account_profile_details_form" class="form" method="POST" action="{{$url}}" enctype="multipart/form-data">
                        {{ method_field($method) }}
                        {{ csrf_field() }}
                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                            
                            <div class="card-body border-top p-9">


                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">เลือกยูสเจ้าของ</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <select class="form-select" aria-label="Select example" name="user_id">
                                            <option> -- เลือกยูสเจ้าของ -- </option>
                                            @isset($user)
                                            @foreach($user as $u)
                                            <option value="{{$u->id}}" @if( $item->user_id == $u->id)
                                                selected='selected'
                                                @endif
                                                >{{$u->name}} - {{$u->phone}}</option>
                                            @endforeach
                                            @endisset
                                        </select>
                                        @if ($errors->has('user_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณาเลือกยูสเจ้าของ</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">ชื่อจริง</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="fname" class="form-control form-control-lg form-control-solid" placeholder="คุณคิม" value="{{$item->fname}}">
                               
                                        @if ($errors->has('fname'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณากรอกชื่อจริง</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">นามสกุล</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="lname" class="form-control form-control-lg form-control-solid" placeholder="นามสกุลผู้ใช้งาน" value="{{$item->lname}}">
                               
                                        @if ($errors->has('lname'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณากรอกนามสกุล</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>


                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">เบอร์ติดต่อ</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="phone" class="form-control form-control-lg form-control-solid" placeholder="094-852-XXXX" value="{{$item->phone}}">
                               
                                        @if ($errors->has('phone'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณากรอกเบอร์ติดต่อ</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>


                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">อีเมล</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="email" class="form-control form-control-lg form-control-solid" placeholder="email@email.com" value="{{$item->email}}">
                               
                                        @if ($errors->has('email'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณากรอกอีเมล</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>

                                
                                <div class="row mb-0">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">เปิดใช้งานทันที</label>
                                    <!--begin::Label-->
                                    <!--begin::Label-->
                                    <div class="col-lg-8 d-flex align-items-center">
                                        <div class="form-check form-check-solid form-switch form-check-custom fv-row">
                                            <input class="form-check-input w-45px h-30px" type="checkbox" id="allowmarketing" name="status" checked="checked" value="1"/>
                                            <label class="form-check-label" for="allowmarketing"></label>
                                        </div>
                                    </div>
                                    <!--begin::Label-->
                                </div>
                            

                            </div>
                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <button type="reset" class="btn btn-light btn-active-light-primary me-2">ยกเลิก</button>
                                <button type="submit" class="btn btn-primary" id="kt_account_profile_details_submit">บันทึกข้อมูล</button>
                            </div>
                        </div>
                    </form>
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


@stop('scripts')
