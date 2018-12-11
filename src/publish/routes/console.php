<?php

use Illuminate\Support\Facades\Artisan;
use Lubart\Just\Models\Version;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('just:install', function () {
    // Publish public files
    Artisan::call("vendor:publish", ["--tag" => "just-public", "--force" => true]);
    exec("ln -s ". base_path('storage/app/public')." ". public_path('storage'));
    updateMixManifest();
    
    mkdir(app_path('Just'), 0775);
    mkdir(app_path('Just/Panel'), 0775);
    mkdir(app_path('Just/Panel/Block'), 0775);
    mkdir(app_path('Just/Panel/Block/Addon'), 0775);
    
    $this->info('Public files were updated!');
    
    // Migrate database
    Artisan::call("migrate", ["--step" => false]);
    
    $this->info('Database structure was created!');
    
    // Seed data
    Artisan::call("db:seed", ["--class" => "Lubart\\Just\\Database\\Seeds\\JustStructureSeeder"]);
    Artisan::call("db:seed", ["--class" => "Lubart\\Just\\Database\\Seeds\\JustDataSeeder"]);
    
    Version::create(['version' => Version::inComposer()]);
    
    $this->info('Data Just! seeded!');
    
})->describe('Install Just! CRM');

Artisan::command('just:update', function () {
    // Publish public files
    Artisan::call("vendor:publish", ["--tag" => "just-public", "--force" => true]);
    updateMixManifest();
    
    $this->info('Public files were updated!');
    
    // Migrate database
    Artisan::call("migrate");

    $this->info('Database structure was updated!');
    
    if(Version::shouldBeUpdated()){
        Artisan::call("db:seed", ["--class" => "Lubart\\Just\\Database\\Seeds\\JustUpdateSeeder"]);
        
        $this->info('New data seeded to the database!');
        
        Version::create(['version' => Version::inComposer()]);
    }
    
})->describe('Update Just! CRM');

Artisan::command('just:seed', function () {
    // Seed Just! data
    Artisan::call("db:seed", ["--class" => "Lubart\\Just\\Database\\Seeds\\JustStructureSeeder"]);
    Artisan::call("db:seed", ["--class" => "Lubart\\Just\\Database\\Seeds\\JustDataSeeder"]);
    
    // Seed project data
    Artisan::call("db:seed");
    
    $this->info('Data were seeded!');
})->describe('Seed data related to Just! and current project');


function updateMixManifest(){
    $justManifest = json_decode(file_get_contents(__DIR__.'/../public/mix-manifest.json'));
    if(file_exists(public_path('mix-manifest.json'))){
        $publicManifest = json_decode(file_get_contents(public_path('mix-manifest.json')));
    }
    else{
        $publicManifest = new \stdClass;
    }
    
    foreach($justManifest as $file=>$code){
        $publicManifest->{$file} = $code;
    }
    
    file_put_contents(public_path('mix-manifest.json'), json_encode($publicManifest));
}

Artisan::command('make:addonMigration {name}', function () {
    $table = $this->argument("name");
    $tableName = preg_replace("/create_|_table/", "", $table);
    $modelTable = explode("_", $tableName)[0];
    $addonTable = explode("_", $tableName)[1];
    $className = implode("", array_map(function($item){
        return ucfirst($item);
    }, explode("_", $table)));

    $lines = [];
    $lines[] = "<?php";
    $lines[] = "";
    $lines[] = "use Illuminate\Support\Facades\Schema;";
    $lines[] = "use Illuminate\Database\Schema\Blueprint;";
    $lines[] = "use Illuminate\Database\Migrations\Migration;";
    $lines[] = "";
    $lines[] = "class ".$className." extends Migration";
    $lines[] = "{";
    $lines[] = "    public function up()";
    $lines[] = "    {";
    $lines[] = "        Schema::create('".$tableName."', function (Blueprint \$table) {";
    $lines[] = "            \$table->engine = 'InnoDB';";
    $lines[] = "";
    $lines[] = "            \$table->increments('id');";
    $lines[] = "            \$table->integer('modelItem_id')->unsigned();";
    $lines[] = "            \$table->integer('addonItem_id')->unsigned();";
    $lines[] = "            \$table->timestamps();";
    $lines[] = "";
    $lines[] = "            \$table->foreign('modelItem_id')->references('id')->on('".$modelTable."')->onUpdate('cascade')->onDelete('cascade');";
    $lines[] = "            \$table->foreign('addonItem_id')->references('id')->on('".$addonTable."')->onUpdate('cascade')->onDelete('cascade');";
    $lines[] = "        });";
    $lines[] = "    }";
    $lines[] = "";
    $lines[] = "    public function down()";
    $lines[] = "    {";
    $lines[] = "        Schema::dropIfExists('".$tableName."');";
    $lines[] = "    }";
    $lines[] = "}";

    $date = new \DateTime;
    $fileName = $date->format("Y_m_d_u_").$table.".php";

    file_put_contents(database_path('migrations/'.$fileName), implode("\n", $lines));

    $this->info('Pivot table was created successfully!');
})->describe('Create pivot table for an addon');


Artisan::command('make:relatedBlockMigration {name}', function () {
    $table = $this->argument("name");
    $tableName = preg_replace("/create_|_table/", "", $table);
    $modelTable = explode("_", $tableName)[0];
    $className = implode("", array_map(function($item) {
                return ucfirst($item);
            }, explode("_", $table)));

    $lines = [];
    $lines[] = "<?php";
    $lines[] = "";
    $lines[] = "use Illuminate\Support\Facades\Schema;";
    $lines[] = "use Illuminate\Database\Schema\Blueprint;";
    $lines[] = "use Illuminate\Database\Migrations\Migration;";
    $lines[] = "";
    $lines[] = "class " . $className . " extends Migration";
    $lines[] = "{";
    $lines[] = "    public function up()";
    $lines[] = "    {";
    $lines[] = "        Schema::create('" . $tableName . "', function (Blueprint \$table) {";
    $lines[] = "            \$table->engine = 'InnoDB';";
    $lines[] = "";
    $lines[] = "            \$table->increments('id');";
    $lines[] = "            \$table->integer('modelItem_id')->unsigned();";
    $lines[] = "            \$table->integer('block_id')->unsigned();";
    $lines[] = "            \$table->timestamps();";
    $lines[] = "";
    $lines[] = "            \$table->foreign('modelItem_id')->references('id')->on('" . $modelTable . "')->onUpdate('cascade')->onDelete('cascade');";
    $lines[] = "            \$table->foreign('block_id')->references('id')->on('blocks')->onUpdate('cascade')->onDelete('cascade');";
    $lines[] = "        });";
    $lines[] = "    }";
    $lines[] = "";
    $lines[] = "    public function down()";
    $lines[] = "    {";
    $lines[] = "        Schema::dropIfExists('" . $tableName . "');";
    $lines[] = "    }";
    $lines[] = "}";

    $date = new \DateTime;
    $fileName = $date->format("Y_m_d_u_") . $table . ".php";

    file_put_contents(database_path('migrations/' . $fileName), implode("\n", $lines));

    $this->info('Pivot table was created successfully!');
})->describe('Create pivot table for related block');


Artisan::command('just:makeBlock {className}', function () {
    $className = ucfirst($this->argument("className"));
    $description = $this->ask('Provide block description');
    $table = $this->ask('Enter block\'s database table');
    
    $lines = [];
    $lines[] = "<?php";
    $lines[] = "";
    $lines[] = "namespace App\Just\Panel\Block;";
    $lines[] = "";
    $lines[] = "use Lubart\Form\FormElement;";
    $lines[] = "use Lubart\Just\Tools\Useful;";
    $lines[] = "use Illuminate\Http\Request;";
    $lines[] = "use Lubart\Just\Structure\Panel\Block\AbstractBlock;";
    $lines[] = "";
    $lines[] = "class " . $className . " extends AbstractBlock";
    $lines[] = "{";
    $lines[] = "    ";
    $lines[] = "    protected \$fillable;";
    $lines[] = "    ";
    $lines[] = "    protected \$table = '".$table."';";
    $lines[] = "    ";
    $lines[] = "    protected \$settingsTitle;";
    $lines[] = "    ";
    $lines[] = "    public function form() {";
    $lines[] = "        return \$this->form;";
    $lines[] = "    }";
    $lines[] = "    ";
    $lines[] = "    public function handleForm(Request \$request) {";
    $lines[] = "        return;";
    $lines[] = "    }";
    $lines[] = "}";

    file_put_contents(app_path('Just/Panel/Block/' . $className . ".php"), implode("\n", $lines));
    
    foreach(Lubart\Just\Models\Theme::all() as $theme){
        file_put_contents(base_path('resources/views/'.$theme->name.'/blocks/' . lcfirst($className) . '.blade.php'), '<div>'.$className.' content</div>');
        file_put_contents(base_path('resources/views/'.$theme->name.'/settings/' . lcfirst($className) . '.blade.php'), "<?php\n/*\nStandard list potentially can be used\n\n@include('Just.settings.list')\n*/\n?>");
    }
    
    Illuminate\Support\Facades\DB::table('blockList')->insert([
        'block' => lcfirst($className),
        'title' => ucfirst($className),
        'description' => $description,
        'table' => $table
    ]);
    
    $this->info('Block was created successfully!');
    
})->describe('Create custom block');


Artisan::command('just:createTheme {themeName}', function () {
    $theme = ucfirst($this->argument("themeName"));
    
    $isExists = !! \Lubart\Just\Models\Theme::where('name', $theme)->first();
    
    if($isExists){
        $error = 'Theme "'.$theme.'" already exists! Create layout for it with master user.';
        $emptyLine = "    ";
        for($i=0; $i<strlen($error); $i++){
            $emptyLine .= " ";
        }
        $this->error("");
        $this->error($emptyLine);
        $this->error('  '.$error.'  ');
        $this->error($emptyLine);
        $this->error("");
        return;
    }
    
    \Lubart\Just\Models\Theme::create([
        'name' => $theme
    ]);
     
    mkdir(public_path('css/'.$theme), 775);
    mkdir(public_path('js/'.$theme), 775);
    mkdir(base_path('resources/views/'.$theme), 775);
    mkdir(base_path('resources/assets/js/'.$theme), 775);
    mkdir(base_path('resources/assets/sass/'.$theme), 775);
    
    $this->info('Theme was created successfully!');
    $this->comment("");
    $this->comment('In order to use this theme related layout should be created!');
    $this->comment('To create new layout do as master user Layout > Create Layout');
    $this->comment("");
    
})->describe('Create custom theme');


Artisan::command('just:makeAddon {className}', function () {
    $className = ucfirst($this->argument("className"));
    $description = $this->ask('Provide addon description');
    $table = $this->ask('Enter addon\'s database table');
    
    $lines = [];
    $lines[] = "<?php";
    $lines[] = "";
    $lines[] = "namespace App\Just\Panel\Block\Addon;";
    $lines[] = "";
    $lines[] = "use Lubart\Form\Form;";
    $lines[] = "use Lubart\Form\FormElement;";
    $lines[] = "use Lubart\Just\Structure\Panel\Block\Addon;";
    $lines[] = "use Illuminate\Http\Request;";
    $lines[] = "use Lubart\Just\Structure\Panel\Block\Addon\AbstractAddon;";
    $lines[] = "";
    $lines[] = "class " . $className . " extends AbstractAddon";
    $lines[] = "{";
    $lines[] = "    ";
    $lines[] = "    protected \$fillable = ['addon_id', 'name'];";
    $lines[] = "    ";
    $lines[] = "    protected \$table = '".$table."';";
    $lines[] = "    ";
    $lines[] = "    /**";
    $lines[] = "     * Update existing settings form and add new elements";
    $lines[] = "     *";
    $lines[] = "     * @param Addon \$addon Addon object";
    $lines[] = "     * @param Form \$form Form object";
    $lines[] = "     */";
    $lines[] = "    public static function updateForm(Addon \$addon, Form \$form, \$values) {";
    $lines[] = "        return \$form;";
    $lines[] = "    }";
    $lines[] = "    ";
    $lines[] = "    public static function handleForm(Addon \$addon, Request \$request, \$item) {";
    $lines[] = "        return;";
    $lines[] = "    }";
    $lines[] = "    ";
    $lines[] = "    public static function validationRules(Addon \$addon) {";
    $lines[] = "        return [];";
    $lines[] = "    }";
    $lines[] = "}";

    file_put_contents(app_path('Just/Panel/Block/Addon/' . $className . ".php"), implode("\n", $lines));
    
    Illuminate\Support\Facades\DB::table('addonList')->insert([
        'addon' => lcfirst($className),
        'title' => ucfirst($className),
        'description' => $description,
        'table' => $table
    ]);
    
    $this->info('Addon was created successfully!');
    
})->describe('Create custom addon');
