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
