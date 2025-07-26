<?php 
    
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

    // get translation file
    $translationFile = 'messages.';
    if(array_key_exists('translationFile', $config)) {
        $translationFile = $config['translationFile'];
    }
?>
<?php if($show) { ?>
    <div class="form-group">
        <label for="{{ $field }}">{{ ucfirst(trans($translationFile.$title)) }}: {{ $required == 'required' ? '*' : '' }}</label>
        <?php foreach($options['all'] as $optionKey => $optionValue) { ?>
            <br /><input type="checkbox" name="{{ $field }}[]" id="{{ $field }}" style="padding:5px" value="{{ $optionKey }}" <?php echo array_key_exists($optionKey, $options['selected']) ? 'checked' : ''; ?> sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }}"> {{ ucfirst($optionValue) }}
        <?php } ?>
    </div>
<?php } ?>