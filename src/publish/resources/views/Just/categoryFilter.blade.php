@if($block->categories()->first())
<div class="button-group filter-button-group">
    <button data-filter="*" class="btn btn-default btn-danger">Show all</button>
    @foreach($block->categories()->first()->values()->get() as $cat)
    <button data-filter=".{{ $cat->value }}" class="btn btn-default">{{ $cat->name }}</button>
    @endforeach
</div>
@endif

<script>
    $(document).ready(function(){
        $('.filter-button-group button').on('click', function() {
            var filterValue = $(this).attr('data-filter');
            $('.dragula-list-item').css('display', 'none');
            $('.dragula-list-item'+filterValue).css('display', 'block');
            $('.filter-button-group button').removeClass('btn-danger');
            $(this).addClass('btn-danger');
        });
    });
</script>