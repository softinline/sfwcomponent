<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] ? '*' : '' }}</label>

    <div id="upload_box"  class="upload__box" style="min-height: 200px">
        <div class="upload__btn-box d-flex justify-content-center" style="min-height: inherit">
          <label class="btn btn-primary btn btn-profile d-flex justify-content-center align-items-center align-self-center">
            + {{ ucfirst(trans('messages.'.$component['title'])) }}
            <input id="{{ $component['field'] }}" name="{{ $component['field'] }}[]" type="file" multiple data-max_length="{{ $component['max-length'] }}" {{ $component['required'] ? 'required' : '' }} class="upload__inputfile {{ $component['required'] ? 'sfwcomponent-frm-item-required' : '' }}">
            <input id="delete-files" type="text" class="d-none" name="delete-files" value="">
            <input id="main_image" type="text" class="d-none" name="main_image" value="">
            <input id="main_image_preuploaded" type="text" class="d-none" name="main_image_preuploaded" value="">
          </label>
        </div>
        <div class="upload__file-wrap">
            {{-- Preload files on edit --}}
            @if (@isset($item))
                @forelse ($item->files as $file)
                    @if ($file->type == 'video')
                    <div class='upload__file-box'>
                        <div class='upload__video-close upload__file-close ml-1 mt-1'></div>
                        <video id="{{$file->id}}" width='200' height='150' controls style="max-width: 100%;">
                            <source src="{{$file->src}}" type='video/mp4' data-number="{{$loop->iteration-1}}" data-file="{{$file->name}}">
                        </video>
                    </div>
                    @elseif ($file->type == 'image')
                    <div class='upload__file-box'>
                        <div class='upload__img-close upload__file-close ml-1 mt-1'></div>
                        <img id="{{$file->id}}" class='w-100 h-100 upload__file-file upload__image-file' src="{{$file->src}}" data-number="{{$loop->iteration-1}}" data-file="{{$file->name}}">
                    </div>
                    @else
                    <div class='upload__file-box'>
                        <div class='upload__img-close upload__file-close ml-1 mt-1'></div>
                        <img id="{{$file->id}}" class='w-100 h-100 upload__file-file' src="{{ url('/vendor/softinline/sfwcomponent/pdf_file_icon.svg') }}" data-number="{{$loop->iteration-1}}" data-file="{{$file->name}}">
                    </div>
                    @endif
                @empty
                @endforelse
            @endif

        </div>
    </div>
</div>
<script>
    $(function() {
        $( document ).ready(function () {
            fileUpload();
        });

        function fileUpload() {
            var fileWrap = "";
            var fileArray = [];
            var deleteFiles = document.querySelector('#delete-files');
            if (deleteFiles) deleteFiles.value = "";
            var main_image_input = document.querySelector('#main_image');
            if (main_image_input) main_image_input.value = "";
            var fileTypes = "{{ $component['extensions'] }}".split(",");  //acceptable file extensions
            
            $('.upload__inputfile').each(function () {
                $(this).on('change', function (e) {
                    $(this).closest('.upload__box').LoadingOverlay('show');
                    fileWrap = $(this).closest('.upload__box').find('.upload__file-wrap');
                    var maxLength = $(this).attr('data-max_length');
                    var preloadedFilesLength = $(".upload__file-file").length;
                    
                    var files = e.target.files;
                    var filesArr = Array.prototype.slice.call(files);

                    if (filesArr.length > maxLength - preloadedFilesLength) {
                            Swal.fire({
                                title: 'Número máximo de archivos superado',
                                text: 'El número máximo de archivos es '+maxLength,
                                icon: 'warning',
                                showCancelButton: false,
                                confirmButtonColor: '#00A350',
                                confirmButtonText: 'Aceptar',
                            });
                        return false;
                    }
                    filesArr.forEach(function (f, index) {
                        // check if the file type is in the accepted extensions array
                        var extension = f.name.split('.').pop().toLowerCase();  //file extension from input file
                        if (fileTypes.indexOf('*') > -1) isSuccess = true;
                        else isSuccess = fileTypes.indexOf(extension) > -1;
                        if (!isSuccess) {
                            Swal.fire({
                                title: 'Formato incorrecto',
                                text: 'La extensión del archivo seleccionado no está soportada',
                                icon: 'warning',
                                showCancelButton: false,
                                confirmButtonColor: '#00A350',
                                confirmButtonText: 'Aceptar',
                            });
                            return;
                        }
                        else {
                            f.main_image = false;

                            var reader = new FileReader();
                            reader.readAsDataURL(f);
                            reader.onload = function (e) {
                                fileArray.push(f);
                                if (f.type.match('image.*')) {
                                    //Initiate the JavaScript Image object.
                                    var image = new Image();
                                    //Set the Base64 string return from FileReader as source.
                                    image.src = e.target.result;
                                    //Validate the File Height and Width.
                                    image.onload = function () {
                                        var html = "<div class='upload__file-box'><div class='upload__img-close upload__file-close ml-1 mt-1'></div><img class='w-100 h-100 upload__file-file upload__image-file' src=\"" + e.target.result + "\" data-number='" + $(".upload__file-close").length + "' data-file='" + f.name + "'></div>";
                                        fileWrap.append(html);
                                    };
                                }
                                else if (f.type.match('video.*')) {
                                    var html = "<div class='upload__file-box'><div class='upload__video-close upload__file-close ml-1 mt-1'></div><video width='200' height='150' controls style=\"max-width: 100%;\"><source src=\""+ e.target.result + "\" type='video/mp4' data-number='" + $(".upload__file-close").length + "' data-file='" + f.name + "'></video></div>";
                                    fileWrap.append(html);
                                }
                                else {
                                    var html = "<div class='upload__file-box'><div class='upload__img-close upload__file-close ml-1 mt-1'></div><img class='w-100 h-100 upload__file-file' src=\"{{ url('/vendor/softinline/sfwcomponent/pdf_file_icon.svg') }}\" data-number='" + $(".upload__file-close").length + "' data-file='" + f.name + "'></div>";
                                    fileWrap.append(html);
                                }
                            }
                        }
                    });
                    $(this).closest('.upload__box').LoadingOverlay('hide');
                });
            });
            
            // remove non videos
            $('body').on('click', ".upload__img-close", function (e) {
                var file = $(this).parent().find('img').data("file");
                var found = false;
                for (var i = 0; i < fileArray.length; i++) {
                    if (fileArray[i].name === file) {
                        fileArray.splice(i, 1);
                        found = true;
                        break;
                    }
                }
                if (!found){
                    var file_id = $(this).parent().find('img');
                    if (!deleteFiles.value) {
                        deleteFiles.value += file_id[0].id;
                    }
                    else {
                        deleteFiles.value += ',';
                        deleteFiles.value += file_id[0].id;
                    }
                }
                $(this).parent().remove();
            });

            // remove videos
            $('body').on('click', ".upload__video-close", function (e) {
                var file = $(this).parent().find('video').find('source').data('file');
                var found = false;
                for (var i = 0; i < fileArray.length; i++) {
                    if (fileArray[i].name === file) {
                        fileArray.splice(i, 1);
                        found = true;
                        break;
                    }
                }
                if (!found){
                    var file_id = $(this).parent().find('video');
                    if (!deleteFiles.value) {
                        deleteFiles.value += file_id[0].id;
                    }
                    else {
                        deleteFiles.value += ',';
                        deleteFiles.value += file_id[0].id;
                    }
                }
                $(this).parent().remove();
            });

            // click on an image to select it as the main image
            $('#upload_box').on('click', '.upload__image-file', function() {
                var main_image = $('.main_image');
                if (main_image) main_image.removeClass('main_image');
                $(this).addClass('main_image');
                var main_image_input = document.getElementById('main_image');
                var main_image_preuploaded_input = document.getElementById('main_image_preuploaded');
                if (this.hasAttribute('id')) {
                    main_image_input.value = "";
                    main_image_preuploaded_input.value = this.id;
                }
                else {
                    if (main_image_preuploaded_input) main_image_preuploaded_input.value = "";
                    main_image_input.value = $(this).data("file");
                }
            });
        };

    });
</script>