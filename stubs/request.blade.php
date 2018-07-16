namespace App\Http\Requests\{{$requestsFolder}};

@if($needToValidate)
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\{{$entity}}Service;
@endif
use Illuminate\Foundation\Http\FormRequest;

class {{$method}}{{$entity}}Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
@if($method == 'Search')
<?php $searchDefault = "    'order_by' => 'string',
            'desc' => 'boolean'
        "; ?>
@else
<?php $searchDefault = ""; ?>
@endif
        return [@if(!empty($parameters))

@foreach($parameters as $parameter)
            '{{$parameter['name']}}' => '{{implode('|', $parameter['rules'])}}',
@endforeach
        <?= $searchDefault ?>
@else
<?= $searchDefault ?>
@endif];
    }

@if($needToValidate)
@if(app()::VERSION < 5.6)
    public function validate()
    {
        parent::validate();

@else
    public function validateResolved()
    {
        parent::validateResolved();

@endif
        $service = app({{$entity}}Service::class);

        if (!$service->exists(['id' => $this->route('id')])) {
            throw new NotFoundHttpException('{{$entity}} does not exist');
        }
    }
@endif
}