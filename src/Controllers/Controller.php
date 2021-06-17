<?php

namespace Just\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Http\Controllers\Controller as LaravelController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Config;
use Just\Models\System\Route;
use Illuminate\Http\Request;
use Just\Models\Block;
use Just\Validators\ValidatorExtended;

class Controller extends LaravelController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct() {
        if(method_exists(LaravelController::class, '__construct')) {
            parent::__construct();
        }

        Config::set('isAdmin', false);
    }

    public function buildPage(Request $request) {
        $route = Route::findByUrl($request->route()->uri);

        $layout = $route->page->layout;

        $relBlock = Block::find($route->block_id);
        if(!is_null($relBlock)){
            $relBlock = $relBlock->specify($request->id);
        }

        $panels = [];
        foreach($layout->panels() as $panel){
            $panels[] = $panel->setPage($route->page);
        }

        $view = file_exists(resource_path('views/'.$layout->name.'/layouts/'.$layout->class.'.blade.php')) ?
                $layout->name.'.layouts.'.$layout->class :
                $layout->name.'.layouts.primary';

        return view($view)->with(['panels'=>$panels, 'block'=>$relBlock, 'layout'=>$layout, 'page'=>$route->page]);
    }

    public function ajax(Request $request) {
        $route = Route::findByUrl($request->route()->uri);

        /**
         * @var Block $block
         */
        $block = Block::findModel($route->block_id, 0);

        return $block->item()->{$route->action}($request);
    }

    public function post(Request $request) {
        $block = Block::find($request->block_id);

        if(!empty($block)){
            $message = $block->specify()->handleItemSetupForm($request, true);
            if($message instanceof ValidatorExtended){
                return redirect()->back()
                        ->withErrors($message, 'errorsFrom' . ucfirst($block->type . $block->id))
                        ->withInput();
            }
        }
        else{
            return redirect()->back();
        }

        return redirect()->back()->with('successMessageFrom' . ucfirst($block->type . $block->id), $message);
    }
}
