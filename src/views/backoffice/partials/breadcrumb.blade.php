<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">PCK - {{ $data['title'] }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <?php foreach($data['items'] as $item) { ?>
                        <?php if(count($item) > 1) { ?>
                            <li class="breadcrumb-item"><a href="{{ $item[1] }}">{{ $item[0] }}</a></li>
                        <?php } else { ?>
                            <li class="breadcrumb-item active">{{ $item[0] }}</li>
                        <?php } ?>
                    <?php } ?>                    
                </ol>
            </div>
        </div>
    </div>
</div>