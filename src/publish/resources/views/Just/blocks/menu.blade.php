<div class="col-md-{{ $block->width }}">
    <?php
    $menuView = function($menu) use (&$menuView){
        $view = "<ul>";
        foreach($menu as $item){

            $view .= "<li class='" . ($item->item->isActive === 0 ? "inactive" : "") . "'>"
                . "<a href=\"". url($item->item->url) ."\">". $item->item->title ."</a>";
            if(!empty($item->sub)){
                $view .= $menuView($item->sub);
            }
            $view .= "</li>";
        }
        $view .= "</ul>";

        return $view;
    };
    ?>

    {!! $menuView($block->content()) !!}
</div>
