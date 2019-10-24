<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\FormElement;
use Lubart\Just\Requests\ChangeMenuRequest;
use Lubart\Just\Structure\Panel\Block\Contracts\ValidateRequest;
use Lubart\Just\Tools\Useful;
use \Illuminate\Support\Facades\Route;
use Spatie\Translatable\HasTranslations;

class Menu extends AbstractBlock
{
    use HasTranslations;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item', 'parent', 'route', 'url', 'orderNo', 'isActive'
    ];

    public $translatable = ['item'];

    protected $table = 'menus';
    
    protected $menu = [];

    public function content() {
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

    public function form() {
        if(is_null($this->form)){
            return;
        }
        
        $this->form->add(FormElement::text(['name'=>'item', 'label'=>__('menu.form.item'), 'value'=>$this->item]));
        $this->form->add(FormElement::select(['name'=>'parent', 'label'=>__('menu.form.parentItem'), 'options'=>$this->itemList($this->content()), 'value'=>$this->parent]));
        $route = \Lubart\Just\Models\Route::findByUrl($this->route);
        $this->form->add(FormElement::select(['name'=>'route', 'label'=>__('menu.form.route'), 'options'=>$this->routes(), 'value'=>$route->id]));
        $this->form->add(FormElement::text(['name'=>'url', 'label'=>__('menu.form.url'), 'value'=>$this->url]));
        
        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        return $this->form;
    }
    
    /**
     * Handle request from the settings form
     * 
     * @param ChangeMenuRequest $request
     * @return Menu
     */
    public function handleForm(ValidateRequest $request) {
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
        
        $this->handleAddons($request, $item);
        
        return $item;
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

    /**
     * Change an order and put model to the specific position
     *
     * @param integer $newPosition new element position
     * @param array $where where statement
     */
    public function moveTo($newPosition, $where = []) {
        if(empty($where)){
            $where = [
                'block_id' => $this->block_id,
                'parent' => $this->parent
            ];
        }

        Useful::moveModelTo($this, $newPosition, $where);
    }
}
