<div class="dragula-list-container-{{ (isset($block->parameters->settingsScale)?$block->parameters->settingsScale:"100") }}">

    <?php
    $menuView = function($menu) use ($block, &$menuView){
        
        $view = "<ul  id='dragula-container'>";
        foreach($menu as $item){
            $url = $item['item']->url ? $item['item']->url : ( (\Config::get('isAdmin')?'admin/':'').$item['item']->route );
            
            $view .= "<li data-no='".$item['item']->orderNo."' data-id='".$item['item']->id."'>"
                        . '<a href="javascript: deleteModel('. $block->id .', '. $item['item']->id .')" title="Delete">
                                <i class="fa fa-trash-alt"></i>
                            </a>
                            <a href="javascript: activate('. ($item['item']->isActive?0:1) .', '. $block->id .', '. $item['item']->id .')" title="'. ($item['item']->isActive?"Deactivate":"Activate") .'">
                                <i class="fa fa-'. ($item['item']->isActive?"eye-slash":"eye") .'"></i>
                            </a>
                            <a href="javascript: move(\'up\', '. $block->id .', '. $item['item']->id .')" title="Move Up">
                                <i class="fa fa-arrow-up"></i>
                            </a>
                            <a href="javascript: move(\'down\', '. $block->id .', '. $item['item']->id .')" title="Move Down">
                                <i class="fa fa-arrow-down"></i>
                            </a>
                            <a href="javascript: openSettings('. $block->id .', '. $item['item']->id .')" title="Edit">
                                <i class="fa fa-pencil-alt"></i>
                            </a> '
                        . "<a href=\"javascript: openSettings(". $block->id .", ". $item['item']->id .")\">". $item['item']->item ."</a>";
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