<div class="col-md-{{ $block->width }}">
    <?php
    $menuView = function($menu) use (&$menuView){
        
        $view = "<ul>";
        foreach($menu as $item){
            $url = $item['item']->url ? $item['item']->url : ( (\Config::get('isAdmin')?'admin/':'').$item['item']->route );
            
            $view .= "<li>"
                        . "<a href=\"". url($url) ."\">". $item['item']->item ."</a>";
            if(!empty($item['sub'])){
                $view .= $menuView($item['sub']);
            }
            $view .= "</li>";
        }
        $view .= "</ul>";
        
        return $view;
    };
    ?>
    
    {!! $menuView($block->content()) !!}
</div>