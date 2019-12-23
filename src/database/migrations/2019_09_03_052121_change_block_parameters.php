<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Just\Structure\Panel\Block;

class ChangeBlockParameters extends Migration
{
    public function __construct() {
        //dd(\Doctrine\DBAL\Types\Type::getTypesMap());
        \Illuminate\Support\Facades\DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('json', 'string');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->json('super_parameters')->default(null)->charset('')->change();
            $table->json('parameters')->default(null)->charset('')->change();
        });

        $procedure = "drop procedure if exists `merge_parameters`;
        
        create procedure `merge_parameters`()
        begin
            declare rowCount int default 0;
            declare spLength int default 0;
            declare rowId int default 0;
            declare r int default 0;
            declare i int default 0;
            
            select count(*) from `blocks` into rowCount;
            
            set r = 0;
                        
            while r < rowCount do
                set i = 0;
                
                select `id` from `blocks` limit r, 1 into rowId;
                
                select json_length(`super_parameters`) from `blocks` where `id` = rowId into spLength;
                
                while i < spLength do
                    UPDATE `blocks` SET `parameters` =
                    JSON_REMOVE(
                            `parameters`,
                            CONCAT(
                                '$.',
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(
                                        JSON_KEYS(`super_parameters`),
                                        CONCAT('$[', i, ']')
                                    )
                                )
                            )
                        )
                    WHERE `id` = rowId;
                    SET i = i + 1;
                end while;
                
                set r = r + 1;
            end while;
            
        end ;";

        DB::unprepared($procedure);

        DB::unprepared("call merge_parameters();");

        DB::unprepared('drop procedure if exists `merge_parameters`;');

        DB::unprepared("update `blocks` set `parameters` = JSON_MERGE(`parameters`, `super_parameters`);");

        Schema::table('blocks', function (Blueprint $table) {
            $table->dropColumn('super_parameters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->string('parameters')->default('{}')->change();
            $table->string('super_parameters')->after('parameters')->default('{}');
        });
    }
}
