<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\FormElement;
use Lubart\Just\Requests\MenuItemChangeRequest;
use Lubart\Just\Tools\Useful;
use \Illuminate\Support\Facades\Route;

class Menu extends AbstractBlock
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item', 'parent', 'route', 'url', 'orderNo', 'isActive'
    ];
    
    protected $settingsTitle = 'Menu Item';
    
    protected $table = 'menus';
    
    protected $menu = [];

    public function content($id = null) {
        if(is_null($id)){
            return $this->menuItems();
        }
        else{
            return $this->find($id);
        }
    }
    
    public function form() {
        if(!is_null($this->id)){
            $this->form->open();
        }
        
        if(empty($this->form->getElements())){
            $this->form->add(FormElement::text(['name'=>'item', 'label'=>'Item', 'value'=>$this->item]));
            $this->form->add(FormElement::select(['name'=>'parent', 'label'=>'Parent Item', 'options'=>$this->itemList($this->content()), 'value'=>$this->parent]));
            $route = \Lubart\Just\Models\Route::findByUrl($this->route);
            $this->form->add(FormElement::select(['name'=>'route', 'label'=>'Route', 'options'=>$this->routes(), 'value'=>$route->id]));
            $this->form->add(FormElement::text(['name'=>'url', 'label'=>'URL', 'value'=>$this->url]));
            $this->form->add(FormElement::submit(['value'=>'Submit']));
        }
        
        return $this->form;
    }
    
    /**
     * Handle request from the settings form
     * 
     * @param MenuItemChangeRequest $request
     * @return Menu
     */
    public function handleForm(MenuItemChangeRequest $request) {
        if(is_null($request->get('id'))){
            $item = new Menu;
            $item->orderNo = Useful::getMaxNo($this->table, ['block_id'=>$request->get('block_id')]);
        }
        else{
            $item = Menu::findOrNew($request->get('id'));
        }
        
        $item->setBlock($request->get('block_id'));
        $item->item = $request->get('item');
        $item->parent = $request->get('parent') == 0 ? null : $request->get('parent');
        $route = \Lubart\Just\Models\Route::find($request->get('route'));
        $item->route = $route->route;
        $item->url = $request->get('url');
        
        $item->save();
        
        return $item;
    }
    
    private function menuItems() {
        $items = $this->orderBy('parent', 'asc')
                ->orderBy('orderNo', 'asc')
                ->where('block_id', $this->block_id);
        if(!\Config::get('isAdmin')){
            $items = $items->where('isActive', 1);
        }
        
        $items = $items->get(); 
        
        return $this->buildMenuLevels($items);
    }
    
    private function buildMenuLevels($items, $parent = null) {
        $level = [];

        if (!empty($items)) {
            foreach ($items as $item) {
                if($item->parent == $parent){
                    $level[$item->id] = [
                        'item' => $item,
                        'sub' => $this->buildMenuLevels($items, $item->id)
                    ];
                }
            }
        }
        
        return $level;
    }
    
    /**
     * Build array with all menu items
     * 
     * @param array $menu whole menu list
     * @param int $itemId current menu item
     * @param array $units subarray list
     * @param int $l menu level
     * @return array
     */
    private function itemList($menu, $itemId = null, &$units = null, $l = 0){
        if(is_null($units)){
            $units = [0=>"Root item"];
        }
       
        $itm = "";
        for ($i = 0; $i < $l; $i++) {
            $itm .= "---- ";
        }
       
        foreach($menu as $item){
            if($item['item']->id != $itemId){
                $units[$item['item']->id] = $itm.$item['item']->item;
                $this->itemList($item['sub'], $itemId, $units, ++$l);
            }
        }
       
        return $units;
    }
    
    /**
     * Get all page routes
     * 
     * @return array
     */
    private function routes() {
        $routes = [];
        
        foreach(\Lubart\Just\Models\Route::where('type', 'page')->get() as $route){
            $routes[$route->id] = $route->route;
        };
        
        return $routes;
    }
    
    /**
     * Change an order
     * 
     * @param string $dir direction, available values are up, down
     * @param array $where where statement
     */
    public function move($dir, $where = []) {
        $where = [
            'block_id' => $this->block_id,
            'parent' => $this->parent
        ];
        
        Useful::moveModel($this, $dir, $where);
    }
    
    public function currentUri() {
        return trim(Route::getFacadeRoot()->current()->uri(), "/");
    }
}
