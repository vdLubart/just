<?php

namespace Just\Models\Blocks;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use stdClass;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Just\Models\Blocks\Contracts\ValidateRequest;
use Just\Tools\Useful;
use Just\Models\System\Route as JustRoute;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperMenu
 */
class Menu extends AbstractItem
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

    public array $translatable = ['item'];

    protected $table = 'menus';

    protected array $menu = [];

    public function content(): object {
        $items = $this->orderBy('parent', 'asc')
            ->orderBy('orderNo', 'asc')
            ->where('block_id', $this->block_id);
        if(!Config::get('isAdmin')){
            $items = $items->where('isActive', 1);
        }

        $items = $items->get();

        return $this->buildMenuLevels($items);
    }

    /**
     * @param Collection $items
     * @param string|null $parent
     * @return object
     */
    private function buildMenuLevels(Collection $items, ?string $parent = null): object {
        $level = new stdClass();

        if (!empty($items)) {
            foreach ($items as $item) {
                if($item->parent == $parent){
                    $level->{'block/' . $item->block_id . "/item/" . $item->id} = [
                        'item' => [
                            'id' => $item->id,
                            'title' => $item->item,
                            'url' => $item->url ?? ( (Config::get('isAdmin')?'admin/':'').$item->route ),
                            'orderNo' => $item->orderNo,
                            'isActive' => $item->isActive
                        ],
                        'sub' => $this->buildMenuLevels($items, $item->id)
                    ];
                }
            }
        }

        return json_decode(json_encode($level), FALSE);
    }

    public function itemForm(): Form {
        if(is_null($this->form)){
            return new Form();
        }

        $this->identifyItemForm();

        $this->form->add(FormElement::text(['name'=>'item', 'label'=>__('menu.form.item'), 'value'=>$this->getTranslations('item'), 'translate'=>true]));
        $this->form->add(FormElement::select(['name'=>'parent', 'label'=>__('menu.form.parentItem'), 'options'=>$this->itemList($this->content()), 'value'=>$this->parent]));
        $route = JustRoute::findByUrl($this->route);
        $this->form->add(FormElement::select(['name'=>'route', 'label'=>__('menu.form.route'), 'options'=>$this->routes(), 'value'=>$route->id]));
        $this->form->add(FormElement::text(['name'=>'url', 'label'=>__('menu.form.url'), 'value'=>$this->url]));

        $this->includeAddons();

        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        return $this->form;
    }

    /**
     * Handle request from the settings form
     *
     * @param ValidateRequest $request
     * @return Menu
     */
    public function handleItemForm(ValidateRequest $request): Menu {
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
        $route = JustRoute::find($request->get('route'));
        $item->route = $route->route;
        $item->url = $request->get('url');

        $item->save();

        $this->handleAddons($request, $item);

        return $item;
    }

    /**
     * Build array with all menu items
     *
     * @param object $menu whole menu list
     * @param int|null $itemId current menu item
     * @param array|null $units subarray list
     * @param int $l menu level
     * @return array
     */
    private function itemList(object $menu, ?int $itemId = null, ?array &$units = null, int $l = 0): ?array {
        if(is_null($units)){
            $units = [0=>"Root item"];
        }

        $itm = str_repeat("---- ", $l);

        foreach($menu as $item){
            if($item->item->id != $itemId){
                $units[$item->item->id] = $itm.$item->item->title;
                $this->itemList($item->sub, $itemId, $units, ++$l);
            }
        }

        return $units;
    }

    /**
     * Get all page routes
     *
     * @return array
     */
    private function routes(): array {
        $routes = [];

        foreach(JustRoute::where('type', 'page')->get() as $route){
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
