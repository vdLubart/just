<div class='col-md-12'>
    <div class='col-md-11'>
        <h4>
            Settings :: Addon
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-close"></i></a>
    </div>
</div>

<div id="page_{{ $addon->id }}_settingsForm">
    {!! $addon->settingsForm()->render() !!}
</div>

<script>
    CKEDITOR.replace('description');
</script>
