use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use RonasIT\Support\Traits\MigrationTrait;

class Create{{$class}}Table extends Migration
{
    use MigrationTrait;

    public function up()
    {
        DB::beginTransaction();

        $this->createTable();

@foreach($relations['belongsToMany'] as $relation)
        $this->createBridgeTable('{{$entity}}', '{{$relation}}');
@endforeach
@foreach($relations['belongsTo'] as $relation)
        $this->addForeignKey('{{$entity}}', '{{$relation}}');
@endforeach
@foreach($relations['hasOne'] as $relation)
        $this->addForeignKey('{{$relation}}', '{{$entity}}', true);
@endforeach
@foreach($relations['hasMany'] as $relation)
        $this->addForeignKey('{{$relation}}', '{{$entity}}', true);
@endforeach

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

@foreach($relations['hasOne'] as $relation)
        $this->dropForeignKey('{{$relation}}', '{{$entity}}', true);
@endforeach
@foreach($relations['hasMany'] as $relation)
        $this->dropForeignKey('{{$relation}}', '{{$entity}}', true);
@endforeach
@foreach($relations['belongsToMany'] as $relation)
        $this->dropBridgeTable('{{$entity}}', '{{$relation}}');
@endforeach
        Schema::drop('{{\Illuminate\Support\Str::plural(snake_case($entity))}}');

        DB::commit();
    }

    public function createTable()
    {
        Schema::create('{{\Illuminate\Support\Str::plural(snake_case($entity))}}', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
@foreach ($fields as $typeName => $fieldNames)
    @foreach($fieldNames as $fieldName)
        @if (empty(explode('-', $typeName)[1]))
            $table->{{ explode('-', $typeName)[0] }}('{{$fieldName}}')->nullable();
        @else
            $table->{{ explode('-', $typeName)[0] }}('{{$fieldName}}');
        @endif
    @endforeach
@endforeach
        });
    }
}