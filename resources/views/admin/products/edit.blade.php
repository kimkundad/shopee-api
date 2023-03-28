@extends('admin.layouts.template')

@section('title')
    <title>shopee-app</title>
    <meta name="description" content=" shopee-app">
@stop
@section('stylesheet')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
                            แก้ไขสินค้า </h1>
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
                            <li class="breadcrumb-item text-muted">แก้ไขสินค้า</li>
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
                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">รูปหลักสินค้า</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8">
                                        <!--begin::Image input-->
                                        <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('{{ url('img/media-image-size.png') }}')">
                                            <!--begin::Preview existing avatar-->
                                            <div class="image-input-wrapper " style="background-image: url({{ url('images/shopee/products/'.$item->img_product) }}); width:380px; height:380px"></div>
                                            <!--end::Preview existing avatar-->
                                            <!--begin::Label-->
                                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="เปลี่ยน รูปซับหมวดหมู่สินค้า">
                                                <i class="bi bi-pencil-fill fs-7"></i>
                                                <!--begin::Inputs-->
                                                <input type="file" name="img_product" accept=".png, .jpg, .jpeg" />
                                                <input type="hidden" name="avatar_remove" />
                                                <!--end::Inputs-->
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Cancel-->
                                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="ยกเลิก รูปซับหมวดหมู่สินค้า">
                                                <i class="bi bi-x fs-2"></i>
                                            </span>
                                            <!--end::Cancel-->
                                            <!--begin::Remove-->
                                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="ลบ รูปซับหมวดหมู่สินค้า">
                                                <i class="bi bi-x fs-2"></i>
                                            </span>
                                            <!--end::Remove-->
                                        </div>
                                        <!--end::Image input-->
                                        <!--begin::Hint-->
                                        <div class="form-text">ขนาดแนะนำแนวนอน เช่น กว้าง 450px สูง 200px ชนิดรูป: png, jpg, jpeg.</div>
                                        @if ($errors->has('cover_img_shop'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณาเลือกรูป cover ร้านค้า</div>
                                            </div>
                                        @endif
                                        <!--end::Hint-->
                                    </div>
                                    <!--end::Col-->
                                </div>


                                <div class="rounded border p-10">
                                    <!--begin::Input group-->
                                    <div class="form-group row">
                                        <!--begin::Label-->
                                        <label class="col-lg-2 col-form-label text-lg-right">Upload Files:</label>
                                        <!--end::Label-->

                                        <!--begin::Col-->
                                        <div class="col-lg-10">
                                            <!--begin::Dropzone-->
                                            <div class="dropzone dropzone-queue mb-2" id="kt_dropzonejs_example_2">
                                                <!--begin::Controls-->
                                                <div class="dropzone-panel mb-lg-0 mb-2">
                                                    <a class="dropzone-select btn btn-sm btn-primary me-2">Attach files</a>
                                                    <a class="dropzone-upload btn btn-sm btn-light-primary me-2">Upload All</a>
                                                    <a class="dropzone-remove-all btn btn-sm btn-light-primary">Remove All</a>
                                                </div>
                                                <!--end::Controls-->

                                                <!--begin::Items-->
                                                <div class="dropzone-items wm-200px">
                                                    <div class="dropzone-item" style="display:none">
                                                        <!--begin::File-->
                                                        <div class="dropzone-file">
                                                            <div class="dropzone-filename" title="some_image_file_name.jpg">
                                                                <span data-dz-name>some_image_file_name.jpg</span>
                                                                <strong>(<span data-dz-size>340kb</span>)</strong>
                                                            </div>

                                                            <div class="dropzone-error" data-dz-errormessage></div>
                                                        </div>
                                                        <!--end::File-->

                                                        <!--begin::Progress-->
                                                        <div class="dropzone-progress">
                                                            <div class="progress">
                                                                <div
                                                                    class="progress-bar bg-primary"
                                                                    role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--end::Progress-->

                                                        <!--begin::Toolbar-->
                                                        <div class="dropzone-toolbar">
                                                            <span class="dropzone-start"><i class="bi bi-play-fill fs-3"></i></span>
                                                            <span class="dropzone-cancel" data-dz-remove style="display: none;"><i class="bi bi-x fs-3"></i></span>
                                                            <span class="dropzone-delete" data-dz-remove><i class="bi bi-x fs-1"></i></span>
                                                        </div>
                                                        <!--end::Toolbar-->
                                                    </div>
                                                </div>
                                                <!--end::Items-->
                                            </div>
                                            <!--end::Dropzone-->

                                            <!--begin::Hint-->
                                            <span class="form-text text-muted">Max file size is 5MB and max number of files is 5.</span>
                                            <!--end::Hint-->
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                </div>
                                <br>

                                <div class="py-5">
                                    <div class="rounded border p-5 pb-0 d-flex flex-wrap">
                                        @isset($img)
                                        @foreach($img as $u)
                                        <div class=" me-5 mb-5">
                                            <div class="d-flex flex-column">
                                                <div class="symbol symbol-150px">
                                                <img src="{{ url('images/shopee/products/'.$u->image) }}" >
                                                </div>
                                                <div class="d-flex justify-content-center">
                                                    <a href="{{ url('api/image_del/'.$u->id) }}" onclick="return confirm('Are you sure?')">
                                                        <img src="{{ url('img/close_img.png') }}" style="height:30px" >
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        @endisset
                                    </div>
                                </div>


                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">เลือกบัญชีเจ้าของร้านค้า</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <select class="form-select" aria-label="Select example" name="user_code">
                                            <option> -- เลือกบัญชีเจ้าของร้านค้า -- </option>
                                            @isset($ownershop)
                                            @foreach($ownershop as $u)
                                            <option value="{{$u->user_code}}" 
                                                @if( $item->user_code == $u->user_code)
                                                selected='selected'
                                                @endif
                                                >{{$u->fname}} - {{$u->lname}}</option>
                                            @endforeach
                                            @endisset
                                        </select>
                                        @if ($errors->has('user_code'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณาเลือกบัญชีเจ้าของร้านค้า</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>


                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">เลือกหมวดหมู่ลินค้า</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <select class="form-select" aria-label="Select example" name="category">
                                            <option> -- เลือกหมวดหมู่ลินค้า -- </option>
                                            @isset($cat)
                                            @foreach($cat as $u)
                                            <option value="{{$u->id}}" 
                                                @if( $item->category == $u->id)
                                                selected='selected'
                                                @endif
                                                >{{$u->cat_name}}</option>
                                            @endforeach
                                            @endisset
                                        </select>
                                        @if ($errors->has('category'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณาเลือกหมวดหมู่ลินค้า</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">รหัสสินค้า </label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="sku" class="form-control form-control-lg form-control-solid" value="{{$item->sku}}" placeholder="รองเท้าฉลามยอดฮิต" >
                               
                                        @if ($errors->has('sku'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณากรอกรหัสสินค้า</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">ชื่อสินค้า</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="name_product" class="form-control form-control-lg form-control-solid" value="{{$item->name_product}}" placeholder="รองเท้าฉลามยอดฮิต" >
                               
                                        @if ($errors->has('name_product'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณากรอกชื่อสินค้า</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">รายละเอียดร้านค้า</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <textarea class="form-control form-control-lg form-control-solid" id="textareaAutosize" placeholder="รายละเอียดร้านค้า..." rows="5" name="detail_product" >{{$item->detail_product}} </textarea>
                                        @if ($errors->has('detail_product'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณากรอกรายละเอียดร้านค้า</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>


                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">ราคาสินค้า</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="price" class="form-control form-control-lg form-control-solid" value="{{$item->price}}" placeholder="1500" value="{{old('price') ? old('price') : ''}}">
                               
                                        @if ($errors->has('price'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณากรอกราคาสินค้า</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">ราคาส่วนลดสินค้า ( % เปอร์เซ็น ถ้าไม่มีใส่เป็น 0)</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="price_sales" class="form-control form-control-lg form-control-solid" value="{{$item->price_sales}}" placeholder="รองเท้าฉลามยอดฮิต" value="{{old('price_sales') ? old('price_sales') : 0}}">
                               
                                        @if ($errors->has('price_sales'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณากรอกราคาส่วนลดสินค้า</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">ราคาต้นทุน</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="cost" class="form-control form-control-lg form-control-solid" value="{{$item->cost}}" placeholder="500" value="{{old('cost') ? old('cost') : ''}}">
                               
                                        @if ($errors->has('cost'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณากรอกราคาต้นทุน</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">สินค้าในสต็อก</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="stock" class="form-control form-control-lg form-control-solid" placeholder="200" value="{{$item->stock}}">
                               
                                        @if ($errors->has('stock'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณากรอกสินค้าในสต็อก</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>


                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">น้ำหนักสินค้า</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="weight" class="form-control form-control-lg form-control-solid" placeholder="20 กรัม" value="{{$item->weight}}">
                               
                                        @if ($errors->has('weight'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>กรุณากรอกน้ำหนักสินค้า</div>
                                            </div>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">ความกว้างของสินค้า</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="width_product" class="form-control form-control-lg form-control-solid" placeholder="20 cm." value="{{$item->width_product}}">
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">ความสูงของสินค้า</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="height_product" class="form-control form-control-lg form-control-solid" placeholder="20 cm."  value="{{$item->height_product}}">
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
                                            <input class="form-check-input w-45px h-30px" type="checkbox" id="allowmarketing" name="status" checked="checked" value="1" />
                                            <label class="form-check-label" for="allowmarketing"></label>
                                        </div>
                                    </div>
                                    <!--begin::Label-->
                                </div>

                                <div class="row mt-10 mb-6">
                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">ชนิดของตัวเลือกสินค้า</label>
                                    <div class="col-lg-8 d-flex align-items-center">

                                        <div class="d-flex flex-column">
                                        <!--begin:Option-->
                                            <label class="d-flex flex-stack mb-5 cursor-pointer">
                                                <!--begin:Label-->
                                                <span class="d-flex align-items-center me-2">
                                                    <!--begin:Icon-->
                                                    <span class="symbol symbol-50px me-6">
                                                        <span class="symbol-label bg-light-primary">
                                                            <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/kt-products/docs/metronic/html/releases/2023-03-24-034008/core/html/src/media/icons/duotune/general/gen017.svg-->
                                                            <span class="svg-icon svg-icon-primary svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M5 8.04999L11.8 11.95V19.85L5 15.85V8.04999Z" fill="currentColor"/>
                                                                <path d="M20.1 6.65L12.3 2.15C12 1.95 11.6 1.95 11.3 2.15L3.5 6.65C3.2 6.85 3 7.15 3 7.45V16.45C3 16.75 3.2 17.15 3.5 17.25L11.3 21.75C11.5 21.85 11.6 21.85 11.8 21.85C12 21.85 12.1 21.85 12.3 21.75L20.1 17.25C20.4 17.05 20.6 16.75 20.6 16.45V7.45C20.6 7.15 20.4 6.75 20.1 6.65ZM5 15.85V7.95L11.8 4.05L18.6 7.95L11.8 11.95V19.85L5 15.85Z" fill="currentColor"/>
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                    </span>
                                                    <!--end:Icon-->

                                                    <!--begin:Info-->
                                                    <span class="d-flex flex-column">
                                                        <span class="fw-bold fs-6">ใช้งานแบบไม่มีตัวเลือก</span>
                                                        <span class="fs-7 text-muted">ระบบจะตัดสต็อกจากแหล่งเดียว</span>
                                                    </span>
                                                    <!--end:Info-->
                                                </span>
                                                <!--end:Label-->

                                                <!--begin:Input-->
                                                <span class="form-check form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio"  name="type" value="1" 
                                                    @if( $item->type == 1)
                                                    checked="checked" 
                                                    @endif
                                                    />
                                                </span>
                                                <!--end:Input-->
                                            </label>
                                            <!--end::Option-->

                                            <!--begin:Option-->
                                            <label class="d-flex flex-stack mb-5 cursor-pointer">
                                                <!--begin:Label-->
                                                <span class="d-flex align-items-center me-2">
                                                    <!--begin:Icon-->
                                                    <span class="symbol symbol-50px me-6">
                                                        <span class="symbol-label bg-light-danger">
                                                            <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/kt-products/docs/metronic/html/releases/2023-03-24-034008/core/html/src/media/icons/duotune/general/gen017.svg-->
                                                            <span class="svg-icon svg-icon-danger svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M5 8.04999L11.8 11.95V19.85L5 15.85V8.04999Z" fill="currentColor"/>
                                                                <path d="M20.1 6.65L12.3 2.15C12 1.95 11.6 1.95 11.3 2.15L3.5 6.65C3.2 6.85 3 7.15 3 7.45V16.45C3 16.75 3.2 17.15 3.5 17.25L11.3 21.75C11.5 21.85 11.6 21.85 11.8 21.85C12 21.85 12.1 21.85 12.3 21.75L20.1 17.25C20.4 17.05 20.6 16.75 20.6 16.45V7.45C20.6 7.15 20.4 6.75 20.1 6.65ZM5 15.85V7.95L11.8 4.05L18.6 7.95L11.8 11.95V19.85L5 15.85Z" fill="currentColor"/>
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                    </span>
                                                    <!--end:Icon-->

                                                    <!--begin:Info-->
                                                    <span class="d-flex flex-column">
                                                        <span class="fw-bold fs-6">ใช้งานแบบตัวเลือกเดียว</span>
                                                        <span class="fs-7 text-muted">สต็อกสินค้าจะถูกแบ่งตัวเลือกที่สร้างไว้</span>
                                                    </span>
                                                    <!--end:Info-->
                                                </span>
                                                <!--end:Label-->

                                                <!--begin:Input-->
                                                <span class="form-check form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" name="type" value="2" 
                                                    @if( $item->type == 2)
                                                    checked="checked" 
                                                    @endif
                                                    />
                                                </span>
                                                <!--end:Input-->
                                            </label>
                                            <!--end::Option-->

                                            <!--begin:Option-->
                                            <label class="d-flex flex-stack cursor-pointer">
                                                <!--begin:Label-->
                                                <span class="d-flex align-items-center me-2">
                                                    <!--begin:Icon-->
                                                    <span class="symbol symbol-50px me-6">
                                                        <span class="symbol-label bg-light-success">
                                                            <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/kt-products/docs/metronic/html/releases/2023-03-24-034008/core/html/src/media/icons/duotune/general/gen017.svg-->
                                                            <span class="svg-icon svg-icon-success svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M5 8.04999L11.8 11.95V19.85L5 15.85V8.04999Z" fill="currentColor"/>
                                                                <path d="M20.1 6.65L12.3 2.15C12 1.95 11.6 1.95 11.3 2.15L3.5 6.65C3.2 6.85 3 7.15 3 7.45V16.45C3 16.75 3.2 17.15 3.5 17.25L11.3 21.75C11.5 21.85 11.6 21.85 11.8 21.85C12 21.85 12.1 21.85 12.3 21.75L20.1 17.25C20.4 17.05 20.6 16.75 20.6 16.45V7.45C20.6 7.15 20.4 6.75 20.1 6.65ZM5 15.85V7.95L11.8 4.05L18.6 7.95L11.8 11.95V19.85L5 15.85Z" fill="currentColor"/>
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                    </span>
                                                    <!--end:Icon-->

                                                    <!--begin:Info-->
                                                    <span class="d-flex flex-column">
                                                        <span class="fw-bold fs-6">ใช้งานแบบ 2 ตัวเลือก</span>
                                                        <span class="fs-7 text-muted">สต็อกสินค้าจะถูกแบ่งตัวเลือกที่สองสร้างไว้</span>
                                                    </span>
                                                    <!--end:Info-->
                                                </span>
                                                <!--end:Label-->

                                                <!--begin:Input-->
                                                <span class="form-check form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" name="type" value="3" @if( $item->type == 3)
                                                    checked="checked" 
                                                    @endif/>
                                                </span>
                                                <!--end:Input-->
                                            </label>
                                            <!--end::Option-->
                                        </div>

                                    </div>
                                </div>


                                <div class="row mb-6 ">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">ตั้งชื่อตัวเลือกที่ 1</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="option1" class="form-control form-control-lg form-control-solid" placeholder="สี, ไซส์, ขนาด"  value="{{$item->option1}}">
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">ตั้งชื่อตัวเลือกที่ 2</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <input type="text" name="option2" class="form-control form-control-lg form-control-solid" placeholder="สี, ไซส์, ขนาด"  value="{{$item->option2}}">
                                    </div>
                                    <!--end::Col-->
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


            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            ตัวเลือกที่1 </h1>
                        <!--end::Title-->
                    </div>
                    <!--end::Page title-->

                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a data-bs-toggle="modal" data-bs-target="#kt_modal_stacked_1" class="btn btn-sm fw-bold btn-primary" >
                            <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/kt-products/docs/metronic/html/releases/2023-01-26-051612/core/html/src/media/icons/duotune/arrows/arr017.svg-->
                            <span class="svg-icon svg-icon-muted svg-icon-1hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.3" d="M11 13H7C6.4 13 6 12.6 6 12C6 11.4 6.4 11 7 11H11V13ZM17 11H13V13H17C17.6 13 18 12.6 18 12C18 11.4 17.6 11 17 11Z" fill="currentColor"/>
                            <path d="M21 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H21C21.6 2 22 2.4 22 3V21C22 21.6 21.6 22 21 22ZM17 11H13V7C13 6.4 12.6 6 12 6C11.4 6 11 6.4 11 7V11H7C6.4 11 6 11.4 6 12C6 12.6 6.4 13 7 13H11V17C11 17.6 11.4 18 12 18C12.6 18 13 17.6 13 17V13H17C17.6 13 18 12.6 18 12C18 11.4 17.6 11 17 11Z" fill="currentColor"/>
                            </svg>
                            </span>
                            <!--end::Svg Icon-->
                            สร้างใหม่
                        </a>
                    </div>



                    <div class="modal fade" tabindex="-1" id="kt_modal_stacked_1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title">สร้างตัวเลือกที่ 1</h3>
                        
                                    <!--begin::Close-->
                                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                    <!--end::Close-->
                                </div>
                                <form method="POST" action="{{ url('admin/post_option1/'.$pro_id) }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                        
                                <div class="modal-body">

                                    <div class="row mw-500px mb-5" data-kt-buttons="true" data-kt-buttons-target=".form-check-image, .form-check-input">
                                        <!--begin::Col-->
                                        @php
                                        $s = 1;    
                                        @endphp
                                        @isset($img)
                                        @foreach($img as $u)

                                        <div class="col-3">
                                            <label class="form-check-image @if($s == 1)
                                            active
                                            @endif ">
                                                <div class="form-check-wrapper">
                                                        <img src="{{ url('images/shopee/products/'.$u->image) }}" style="width:100%">
                                                </div>
                                    
                                                <div class="form-check form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" value="{{ $u->id }}" name="img_option1"
                                                    @if($s == 1)
                                                    checked
                                                    @endif
                                                    />
                                                    <div class="form-check-label">
                                                        รูปที่ {{ $s }}
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <!--end::Col-->
                                        @php
                                        $s++;    
                                        @endphp
                                        @endforeach
                                        @endif

                                        <div class="mb-5">
                                            <br>
                                            <label class="form-label required">ชื่อ</label>
                                            <input type="text" class="form-control" name="op_name" placeholder="สีแดง">
                                        </div>
                                   
                                        <div class="mb-5">
                                            <br>
                                            <label class="form-label required">ราคาสินค้า</label>
                                            <input type="text" class="form-control" name="price" placeholder="1200">
                                        </div>

                                        <div class="mb-5">
                                            <label class="form-label required">จำนวนสินค้า</label>
                                            <input type="text" class="form-control" name="stock" value="{{old('stock') ? old('stock') : 0}}" placeholder="500">
                                            <input type="hidden" name="type_product" value="{{ $item->type }}">
                                            <input type="hidden" name="id_product" value="{{ $item->type }}">
                                        </div>

                                        <div class="mb-5">
                                            <label class="form-label required">รหัสสินค้า</label>
                                            <input type="text" class="form-control" name="sku" placeholder="SDT025200">
                                        </div>

                                        <div class="mb-5">
                                            <label class="form-label">เปิดใช้งาน</label>
                                            <div class="form-check form-check-solid form-switch form-check-custom fv-row">
                                                <input class="form-check-input w-45px h-30px" type="checkbox" id="allowmarketing" name="status" checked="checked" value="1" />
                                                <label class="form-check-label" for="allowmarketing"></label>
                                            </div>
                                        </div>

                                       
                                    </div>

                                    
                                </div>
                        
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">ปิด</button>
                                    <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                                </div>
                                </form>
                            </div>
                        </div>
                        </div>
                    
                    
                    </div>


                    
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->

            <div id="kt_app_content" class="app-content flex-column-fluid ">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">

                    

                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-body border-top p-9">


                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table align-middle gs-0 gy-5">
                                    <!--begin::Table head-->
                                    <thead>
                                        <tr>
                                            <th class="p-0 w-50px"></th>
                                            <th class="p-0 min-w-100px"></th>
                                            <th class="p-0 min-w-50px"></th>
                                            <th class="p-0 min-w-50px"></th>
                                            <th class="p-0 min-w-40px"></th>
                                        </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>

                                        @if(isset($option))
                                        @foreach($option as $u)
                                        <tr>
                                            <th>
                                                <img src="{{ url('images/shopee/products/'.$u->img_name) }}" class=" align-self-center" style="height:70px">
                                            </th>
                                            <td>
                                                <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $u->op_name }}</a>
                                            </td>
                                            <td>
                                                {{ $u->price }} บาท
                                            </td>
                                            <td>
                                                {{ $u->stock }} / ชิ้น
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end flex-shrink-0">

                                                    @if($item->type == 3)
                                                    <a data-bs-toggle="modal" data-bs-target="#kt_modal_addsuboption{{ $u->id }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                        <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/kt-products/docs/metronic/html/releases/2023-03-24-172858/core/html/src/media/icons/duotune/general/gen041.svg-->
                                                            <span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
                                                                <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="currentColor"/>
                                                                <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="currentColor"/>
                                                                </svg>
                                                            </span>
                                                        <!--end::Svg Icon-->
                                                    </a>
                                                    @endif
                                                    <a href="{{url('admin/options/'.$u->id.'/edit')}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                        <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </a>
                                                    <a href="{{ url('api/del_options/'.$u->id) }}" onclick="return confirm('Are you sure?')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
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

                                        <div class="modal fade" tabindex="-1" id="kt_modal_addsuboption{{ $u->id }}">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title">สร้างตัวเลือกที่ 2</h3>
                                            
                                                        <!--begin::Close-->
                                                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                                                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                                        </div>
                                                        <!--end::Close-->
                                                    </div>
                                                    <form method="POST" action="{{ url('admin/post_sup_option1/'.$u->id) }}" enctype="multipart/form-data">
                                                        {{ csrf_field() }}
                                            
                                                    <div class="modal-body">
                    
                                                        <div class="row mw-500px mb-5" data-kt-buttons="true" data-kt-buttons-target=".form-check-image, .form-check-input">
                                                            <!--begin::Col-->
                    
                                                            <div class="mb-5">
                                                                <br>
                                                                <label class="form-label required">ชื่อ</label>
                                                                <input type="text" class="form-control" name="op_name" placeholder="ไซส์ XL, L, M">
                                                            </div>
                                                       
                                                            <div class="mb-5">
                                                                <br>
                                                                <label class="form-label required">ราคาสินค้า</label>
                                                                <input type="text" class="form-control" name="price" placeholder="1200">
                                                            </div>
                    
                                                            <div class="mb-5">
                                                                <label class="form-label required">จำนวนสินค้า</label>
                                                                <input type="text" class="form-control" name="stock" value="{{old('stock') ? old('stock') : 0}}" placeholder="500">
                                                                <input type="hidden" name="id_product" value="{{ $pro_id }}">
                                                            </div>
                    
                                                            <div class="mb-5">
                                                                <label class="form-label required">รหัสสินค้า</label>
                                                                <input type="text" class="form-control" name="sku" placeholder="SDT025200">
                                                            </div>
                    
                                                            <div class="mb-5">
                                                                <label class="form-label">เปิดใช้งาน</label>
                                                                <div class="form-check form-check-solid form-switch form-check-custom fv-row">
                                                                    <input class="form-check-input w-45px h-30px" type="checkbox" id="allowmarketing" name="status" checked="checked" value="1" />
                                                                    <label class="form-check-label" for="allowmarketing"></label>
                                                                </div>
                                                            </div>
                    
                                                           
                                                        </div>
                    
                                                        
                                                    </div>
                                            
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">ปิด</button>
                                                        <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                            </div>
                                        
                                        
                                        </div>
                                        @endforeach
                                        @endif
                                        
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            <!--end::Content-->


            @if($item->type == 3)
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            ตัวเลือกที่ 2 </h1>
                        <!--end::Title-->
                    </div>
                    <!--end::Page title-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->

            <div id="kt_app_content" class="app-content flex-column-fluid mb-20">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">

                    

                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-body border-top p-9">


                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table align-middle gs-0 gy-5">
                                    <!--begin::Table head-->
                                    <thead>
                                        <tr>
                                            <th class="p-0 ">ตัวเลือกที่ 1</th>
                                            <th class="p-0 min-w-100px">ชื่อตัวเลือกที่ 2</th>
                                            <th class="p-0 min-w-50px">ราคา</th>
                                            <th class="p-0 min-w-50px">สต๊อกสินค้า</th>
                                            <th class="p-0 ">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>

                                        @if(isset($sub))
                                        @foreach($sub as $u)
                                        <tr>
                                            <th>
                                                <img src="{{ url('images/shopee/products/'.$u->img_name) }}" class=" align-self-center" style="height:50px">
                                                <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $u->op_name }}</a>
                                            </th>
                                            <td>
                                                <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $u->sub_op_name }}</a>
                                            </td> 
                                            <td>
                                                {{ $u->pricex }} บาท
                                            </td>
                                            <td>
                                                {{ $u->stockx }} / ชิ้น
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end flex-shrink-0">

                                                    
                                                    <a href="{{url('admin/options/'.$u->id_q.'/edit')}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                        <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </a>
                                                    <a href="{{ url('api/del_suboptions/'.$u->id_q) }}" onclick="return confirm('Are you sure?')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
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
                                        @endif
                                        
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            <!--end::Content-->
            @endif


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


        <!--begin::Toast-->
<div id="kt_docs_toast_stack_container" class="toast-container position-fixed end-0 p-3 z-index-3" style="top: 80px!important;">
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-kt-docs-toast="stack">
        <div class="toast-header">
            <span class="svg-icon svg-icon-2 svg-icon-success me-3"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.3" d="M7.16973 20.95C6.26973 21.55 5.16972 20.75 5.46972 19.75L7.36973 14.05L2.46972 10.55C1.56972 9.95005 2.06973 8.55005 3.06973 8.55005H20.8697C21.9697 8.55005 22.3697 9.95005 21.4697 10.55L7.16973 20.95Z" fill="currentColor"></path>
                <path d="M11.0697 2.75L7.46973 13.95L16.9697 20.85C17.8697 21.45 18.9697 20.65 18.6697 19.65L13.1697 2.75C12.7697 1.75 11.3697 1.75 11.0697 2.75Z" fill="currentColor"></path>
                </svg>
                </span>
            <strong class="me-auto">ยินดีด้วย!</strong>
            <small>1 mins ago</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            คุณได้ทำการอัพโหลดรูปสำเร็จ
        </div>
    </div>
</div>
<!--end::Toast-->

@endsection

@section('scripts')



<script>
    

    const container = document.getElementById('kt_docs_toast_stack_container');
const targetElement = document.querySelector('[data-kt-docs-toast="stack"]');

    // set the dropzone container id
const id = "#kt_dropzonejs_example_2";
const dropzone = document.querySelector(id);
const newToast = targetElement.cloneNode(true);
    container.append(newToast);

    // Create new toast instance --- more info: https://getbootstrap.com/docs/5.1/components/toasts/#getorcreateinstance
    const toast = bootstrap.Toast.getOrCreateInstance(newToast);

// set the preview element template
var previewNode = dropzone.querySelector(".dropzone-item");
previewNode.id = "";
var previewTemplate = previewNode.parentNode.innerHTML;
previewNode.parentNode.removeChild(previewNode);

var myDropzone = new Dropzone(id, { // Make the whole body a dropzone
    url: "{{ url('api/upload_img_product/'.$pro_id) }}", // Set the url for your upload script location
    parallelUploads: 10,
    acceptedFiles: "image/*",
    previewTemplate: previewTemplate,
    maxFilesize: 10, // Max filesize in MB
    autoQueue: false, // Make sure the files aren't queued until manually added
    previewsContainer: id + " .dropzone-items", // Define the container to display the previews
    clickable: id + " .dropzone-select", // Define the element that should be used as click trigger to select files.
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
});

myDropzone.on("addedfile", function (file) {
    // Hookup the start button
    file.previewElement.querySelector(id + " .dropzone-start").onclick = function () { myDropzone.enqueueFile(file); };
    const dropzoneItems = dropzone.querySelectorAll('.dropzone-item');
    dropzoneItems.forEach(dropzoneItem => {
        dropzoneItem.style.display = '';
    });
    dropzone.querySelector('.dropzone-upload').style.display = "inline-block";
    dropzone.querySelector('.dropzone-remove-all').style.display = "inline-block";
});

// Update the total progress bar
myDropzone.on("totaluploadprogress", function (progress) {
    const progressBars = dropzone.querySelectorAll('.progress-bar');
    progressBars.forEach(progressBar => {
        progressBar.style.width = progress + "%";
    });
});

myDropzone.on("sending", function (file) {
    // Show the total progress bar when upload starts
    const progressBars = dropzone.querySelectorAll('.progress-bar');
    progressBars.forEach(progressBar => {
        progressBar.style.opacity = "1";
    });
    // And disable the start button
    file.previewElement.querySelector(id + " .dropzone-start").setAttribute("disabled", "disabled");
});

// Hide the total progress bar when nothing's uploading anymore
myDropzone.on("complete", function (progress) {
    const progressBars = dropzone.querySelectorAll('.dz-complete');

    setTimeout(function () {
        progressBars.forEach(progressBar => {
            progressBar.querySelector('.progress-bar').style.opacity = "0";
            progressBar.querySelector('.progress').style.opacity = "0";
            progressBar.querySelector('.dropzone-start').style.opacity = "0";
        });
    }, 300);
});

// Setup the buttons for all transfers
dropzone.querySelector(".dropzone-upload").addEventListener('click', function () {
    myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
});

// Setup the button for remove all files
dropzone.querySelector(".dropzone-remove-all").addEventListener('click', function () {
    dropzone.querySelector('.dropzone-upload').style.display = "none";
    dropzone.querySelector('.dropzone-remove-all').style.display = "none";
    myDropzone.removeAllFiles(true);
});

// On all files completed upload
myDropzone.on("queuecomplete", function (progress) {
    const uploadIcons = dropzone.querySelectorAll('.dropzone-upload');
    uploadIcons.forEach(uploadIcon => {
        uploadIcon.style.display = "none";

    // Toggle toast to show --- more info: https://getbootstrap.com/docs/5.1/components/toasts/#show
    toast.show();

    setTimeout(function() {
        location.reload();
    }, 3000);

    });
});

// On all files removed
myDropzone.on("removedfile", function (file) {
    if (myDropzone.files.length < 1) {
        dropzone.querySelector('.dropzone-upload').style.display = "none";
        dropzone.querySelector('.dropzone-remove-all').style.display = "none";
    }
});

</script>


@stop('scripts')
