<?php

namespace SoluzioneSoftware\LaravelMediaLibrary\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use SoluzioneSoftware\LaravelMediaLibrary\Rules\ClassName;
use SoluzioneSoftware\LaravelMediaLibrary\Traits\HasMedia;
use SoluzioneSoftware\LaravelMediaLibrary\Traits\ValidatesMedia;

abstract class PendingMediaRequest extends FormRequest
{
    use ValidatesMedia;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'model_class' => ['bail', 'required', 'string', new ClassName], // fixme: check HasMedia contract implementation
            'collection_name' => ['required', 'string', 'bail'],
        ];

        $model = $this->getModelClass();
        if (!class_exists($model)){
            return $rules;
        }

        return array_merge($rules, $this->getMediaRules());
    }

    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Configure the validator instance.
     *
     * @param  Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator){
            $collection = $this->getCollectionName();
            if (is_null($collection)){
                return;
            }

            if (!in_array($collection, $this->getCollections())){
                $validator->addFailure('collection_name', 'in');
            }
        });
    }

    abstract protected function getMediaRules();

    protected function getModelClass()
    {
        return $this->get('model_class');
    }

    protected function getCollectionName()
    {
        return $this->get('collection_name');
    }

    protected function getCollections()
    {
        /** @var HasMedia|string $modelClass */
        $modelClass = $this->getModelClass();
        return class_exists($modelClass)
            ? array_keys($modelClass::getMediaMediaLibraryCollections())
            : [];
    }
}
