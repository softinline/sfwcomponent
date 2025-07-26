<?php 

    // options
    $method = $component['selector'];                                                    
    $options = $controller::$method(@$item, @$id);

    // field
    $field = $component['field'];

    // title
    $title = $component['title'];

    // required
    $required = false;
    if(isset($component['required'])) {
        $required = $component['required'];
    }

    // disabled
    $disabled = false;
    if(isset($component['disabled'])) {
        $disabled = $component['disabled'];
    }

    // class    
    $class = '';
    if(isset($component['class'])) {
        $class = $component['class'];
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
        <label>{{ ucfirst(trans($translationFile.$title)) }}: {{ $required ? '*' : '' }}</label>
        <select name="{{ $field }}[]" id="{{ $field }}" class="form-control {{ $class }} {{ $required ? 'sfwcomponent-frm-item-required' : '' }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} multiple="true" sfwcomponent-data-title="{{ ucfirst(trans($translationFile.$title)) }}">
            <?php foreach($options['all'] as $optionKey => $optionValue) { ?>
                <option style="padding:5px" value="{{ $optionKey }}" <?php echo array_key_exists($optionKey, $options['selected']) ? 'selected' : ''; ?>>{{ ucfirst($optionValue) }}</option>
            <?php } ?>
        </select>
    </div>
<?php } ?>