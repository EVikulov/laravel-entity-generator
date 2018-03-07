<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 19.10.16
 * Time: 8:36
 */

namespace RonasIT\Support\Generators;

use Carbon\Carbon;
use Illuminate\Support\Str;
use RonasIT\Support\Events\SuccessCreateMessage;

class MigrationsGenerator extends EntityGenerator
{
    protected $migrations;

    public function generate() {
        $entities = $this->getTableName($this->model);
        $fields = $this->fields;

        foreach ($fields as $typeName => $fieldNames) {
            unset($fields[$typeName]);
            empty($fieldNames) ?: $fields = array_merge($fields, array_fill_keys($fieldNames, $typeName));
        }

        $content = $this->getStub('migration', [
            'class' => $this->getTableClassName($this->model),
            'entity' => $this->model,
            'entities' => $entities,
            'relations' => $this->relations,
            'fields' => $fields
        ]);
        $now = Carbon::now()->format('Y_m_d_His');

        $this->saveClass('migrations', "{$now}_create_{$entities}_table", $content);

        event(new SuccessCreateMessage("Created a new Migration: create_{$entities}_table"));
    }
}