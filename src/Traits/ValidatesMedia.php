<?php


namespace SoluzioneSoftware\LaravelMediaLibrary\Traits;


use Illuminate\Support\Arr;

trait ValidatesMedia
{
    protected function appendStoreMediaRules(array $rules, string $modelClass)
    {
        return array_merge($rules, $this->storeMediaRules($modelClass));
    }

    protected function appendUpdateMediaRules(array $rules, string $modelClass)
    {
        return array_merge($rules, $this->updateMediaRules($modelClass));
    }

    /**
     * @param string|HasMedia $modelClass
     * @return array
     */
    protected function storeMediaRules($modelClass)
    {
//         request data
//        [
//            // other fields
//            'media' => [
//                'store' => [
//                    '<collectionName>' => [
//                        [
//                            'file' => '...',
//                            // or
//                            'pending_media_id' => '<pendingMediaId>',
//                            'properties' => [
//                                'key1' => 'Value1',
//                                // other properties...
//                            ]
//                        ],
//                        // other media...
//                    ]
//                ]
//            ]
//        ];

        $rules = [
            'media' => 'array',
            'media.store' => 'array',
        ];

        $collections = array_keys($modelClass::getMediaMediaLibraryCollections());
        foreach ($collections as $collection){
            $commonRules = $this->getValidationCommonRules($modelClass, $collection);
            $creationRules = $this->getValidationCreationRules($modelClass, $collection);

            $commonRulesForCollection = Arr::get($commonRules, 'collection', []);
            $creationRulesForCollection = Arr::get($creationRules, 'collection', []);
            $commonRulesForFile = Arr::get($commonRules, 'file', []);
            $creationRulesForFile = Arr::get($creationRules, 'file', []);

            $rulesForCollection = array_merge($commonRulesForCollection, $creationRulesForCollection);
            $rulesForFile = array_merge($commonRulesForFile, $creationRulesForFile);

            $rules["media.store.$collection"] = $rulesForCollection;
            $rules["media.store.$collection.*.file"] = $rulesForFile;

            $rules["media.store.$collection.*.pending_media_id"] = ['required_without:' . "media.store.$collection.*.file", 'exists:pending_media,id'];
        }

        return $rules;
    }

    /**
     * @param string|HasMedia $modelClass
     * @param string $collection
     * @return array
     */
    protected function storePendingMediaRules($modelClass, string $collection)
    {
//         request data
//        [
//            'media' => [
//                'file' => '...',
//                'properties' => [
//                    'key1' => 'Value1',
//                    // other properties...
//                ]
//            ]
//        ];

        $rules = [
            'media' => ['required', 'array'],
        ];

        $commonRules = $this->getValidationCommonRules($modelClass, $collection);
        $creationRules = $this->getValidationCreationRules($modelClass, $collection);

        $commonRulesForFile = array_merge(Arr::get($commonRules, 'file', []), ['required']);
        $creationRulesForFile = Arr::get($creationRules, 'file', []);

        $rules["media.file"] = array_merge($commonRulesForFile, $creationRulesForFile);

        return $rules;
    }

    /**
     * @param string|HasMedia $modelClass
     * @return array
     */
    protected function updateMediaRules($modelClass)
    {
//        request data
//        [
//            // other fields
//            'media' => [
//                'store' => [
//                    '<collectionName>' => [
//                        [
//                            'file' => '...',
//                            // or
//                            'pending_media_id' => '<pendingMediaId>',
//                        ],
//                        // ...
//                    ]
//                ],
//                'update' => [
//                    [
//                        'id' => '<mediaId>',
//                        'file' => '...',
//                        // or
//                        'pending_media_id' => '<pendingMediaId>',
//                    ],
//                    // ...
//                ],
//                'delete' => [
//                    '<mediaId>',
//                    // ...
//                ],
//            ]
//        ]

        $rules = [
            'media' => 'array',
            'media.store' => 'array',
            'media.update' => 'array',
            'media.delete' => 'array',
        ];

        $collections = array_keys($modelClass::getMediaMediaLibraryCollections());
        foreach ($collections as $collection){
            $commonRules = $this->getValidationCommonRules($modelClass, $collection);
            $updateRules = $this->getValidationUpdateRules($modelClass, $collection);

            $commonRulesForCollection = Arr::get($commonRules, 'collection', []);
            $creationRulesForCollection = Arr::get($updateRules, 'collection', []);
            $commonRulesForFile = Arr::get($commonRules, 'file', []);
            $creationRulesForFile = Arr::get($updateRules, 'file', []);

            $rulesForCollection = array_merge($commonRulesForCollection, $creationRulesForCollection);
            $rulesForFile = array_merge($commonRulesForFile, $creationRulesForFile);

            $rules["media.store.$collection"] = $rulesForCollection;
            $rules["media.update.$collection"] = $rulesForCollection;
            $rules["media.store.$collection.*.file"] = $rulesForFile;
            $rules["media.update.*.file"] = $rulesForFile;

            $rules["media.store.$collection.*.pending_media_id"] = ['required_without:' . "media.store.$collection.*.file", 'exists:pending_media,id'];
            $rules["media.update.*.pending_media_id"] = ['required_without:' . "media.update.*.file", 'exists:pending_media,id'];

            $rules = Arr::add($rules, "media.delete.*", 'integer'); // fixme: use 'exists' rule
        }

        return $rules;
    }

    /**
     * @param string|HasMedia $modelClass
     * @param string $collection
     * @return array
     */
    protected function updatePendingMediaRules($modelClass, string $collection)
    {
//         request data
//        [
//            'media' => [
//                'file' => '...',
//                'properties' => [
//                    'key1' => 'Value1',
//                    // other properties...
//                ]
//            ]
//        ];

        $rules = [
            'media' => ['required', 'array'],
        ];

        $commonRules = $this->getValidationCommonRules($modelClass, $collection);
        $updateRules = $this->getValidationUpdateRules($modelClass, $collection);

        $commonRulesForFile = array_merge(Arr::get($commonRules, 'file', []), ['required']);
        $updateRulesForFile = Arr::get($updateRules, 'file', []);

        $rules["media.file"] = array_merge($commonRulesForFile, $updateRulesForFile);

        return $rules;
    }

    /**
     * @param string|HasMedia $modelClass
     * @param string $collectionName
     * @param string $type values: rules, creation_rules, update_rules
     * @return array
     */
    private function getValidationRules($modelClass, string $collectionName, string $type = 'rules')
    {
        return Arr::wrap(Arr::get($modelClass::getMediaMediaLibraryValidation(), "$collectionName.$type"));
    }

    /**
     * @param string|HasMedia $modelClass
     * @param string $collectionName
     * @return array
     */
    private function getValidationCommonRules($modelClass, string $collectionName)
    {
        return $this->getValidationRules($modelClass, $collectionName);
    }

    /**
     * @param string|HasMedia $modelClass
     * @param string $collectionName
     * @return array
     */
    private function getValidationCreationRules($modelClass, string $collectionName)
    {
        return $this->getValidationRules($modelClass, $collectionName, 'creation_rules');
    }

    /**
     * @param string|HasMedia $modelClass
     * @param string $collectionName
     * @return array
     */
    private function getValidationUpdateRules($modelClass, string $collectionName)
    {
        return $this->getValidationRules($modelClass, $collectionName, 'update_rules');
    }
}
