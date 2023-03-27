<div class="btn-group">
    <button type="button" class="{{ $component['class'] }}">{{ ucfirst(trans('messages.'.$component['title'])) }}</button>
    <button type="button" class="{{ $component['class'] }} dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
        <span class="sr-only">Toggle Dropdown</span>
        <div class="dropdown-menu dropdown-menu-right" role="menu" x-placement="bottom-start">
            <?php foreach($component['options'] as $option) { ?>
                <?php 
                    // prepare action link
                    $tmp = explode(':', $option['action']);
                ?>
                <?php if($tmp[0] == 'js') { ?>
                    <a href="javascript:void(0)" class="dropdown-item" onclick="{{ $tmp[1] }}(this)"><i class="{{ $option['icon'] }}"></i> {{ ucfirst(trans('messages.'.$option['title'])) }}</a>
                <?php } else { ?>                    
                    <a href="javascript:void(0)" class="dropdown-item" onclick="window.open('{{ url($tmp[1]) }}','_self')"><i class="{{ $option['icon'] }}"></i> {{ ucfirst(trans('messages.'.$option['title'])) }}</a>
                <?php } ?>
            <?php } ?>
        </div>
    </button>                                
</div>