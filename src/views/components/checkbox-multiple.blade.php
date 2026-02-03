<?php 

    // before render    
    if(array_key_exists('beforeRender', $component)) {
        $method = $component['beforeRender'];
        $component = $controller::$method($component, @$item);
    }
    
    $field = $component['field'];

    $title = $component['title'];

    $method = $component['selector'];
    $options = $controller::$method(@$item, @$id);

    // required
    $required = false;
    if(isset($component['required'])) {
        $required = $component['required'];
    }

    // show condition
    $show = true;
    if(array_key_exists('beforeShow', $component)) {
        $method = $component['beforeShow'];
        $show = $controller::$method(@$item);
    }

    // search bar
    $searchBar = false;
    if(isset($component['searchBar'])) {
        $searchBar = $component['searchBar'];
    }

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }

    // add help text
    $help = false;
    if(isset($component['help'])) {
        $help = ucfirst(trans($translationFile.$component['help']));
    }
    
?>
<?php if($show) { ?>
    <div class="form-group">
        <label for="{{ $field }}" <?php echo $help != '' ? 'title="'.$help.'"' : '';?> <?php echo $help != '' ? 'class="sfwcomponent-help-text"' : '';?> >{{ ucfirst(trans($translationFile.$title)) }} {{ $required ? '*' : '' }}</label>
        <?php if($searchBar) { ?>
            <input type="text" class="form-control mb-2" name="search-bar-{{ $field }}" id="search-bar-{{ $field }}" placeholder="{{ ucfirst(trans($translationFile.'search')) }}">
        <?php } ?>
        <?php foreach($options['all'] as $optionKey => $optionValue) { ?>
            <div class="option-search-bar-{{ $field }}" data-title="{{ mb_strtolower($optionValue) }}">
                <input type="checkbox" name="{{ $field }}[]" id="{{ $field }}" style="padding:5px" value="{{ $optionKey }}" <?php echo array_key_exists($optionKey, $options['selected']) ? 'checked' : ''; ?> sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }}"> {{ ucfirst($optionValue) }}
            </div>
        <?php } ?>
    </div>
        <script>
            $('#search-bar-{{ $field }}').keyup(function() {
            var value = $(this).val().toLowerCase();
            if(value != '') {
                $(".option-search-bar-{{ $field }}").hide();            
                $(".option-search-bar-{{ $field }}[data-title*='"+value+"']").show();
            }
            else {
                $(".option-search-bar-{{ $field }}").show();        
            }
        });
    </script>
<?php } ?>