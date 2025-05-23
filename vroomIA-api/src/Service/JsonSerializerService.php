<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * Service who handles JSON serialization and deserialization for entities
 */
class JsonSerializerService
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * Create an entity from JSON data
     *
     * @param string $jsonData JSON data to deserialize
     * @param string $entityClass Class name of the entity to create
     * @param array $context Context for deserialization (optional)
     * @return array Array containing the created entity and any errors
     */    
    
    public function createEntityFromJson(string $jsonData, string $entityClass, array $context = []): array
    {
        try {
            $entity = $this->serializer->deserialize($jsonData, $entityClass, 'json', $context);
            
            $decodedData = json_decode($jsonData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedData)) {
                $entity = $this->applyDefaultValues($entity, $decodedData);
            }
            
            $errors = $this->validator->validate($entity);
            
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }
                
                return [
                    'success' => false,
                    'entity' => null,
                    'errors' => $errorMessages
                ];
            }
            
            $this->entityManager->persist($entity);
            // $this->entityManager->flush();
            
            return [
                'success' => true,
                'entity' => $entity,
                'errors' => []
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'entity' => null,
                'errors' => ['message' => $e->getMessage()]
            ];
        }
    }

    /**
     * Update an existing entity from JSON data
     *
     * @param string $jsonData JSON data to deserialize
     * @param object $existingEntity Existing entity to update
     * @return array Array containing the updated entity and any errors
     */
    public function updateEntityFromJson(string $jsonData, object $existingEntity): array
    {
        try {
            $decodedData = json_decode($jsonData, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedData)) {
                throw new \Exception('JSON invalide: ' . json_last_error_msg());
            }
            
            $filteredData = [];
            foreach ($decodedData as $key => $value) {
                // Ne conserver que les champs qui ne sont pas vides
                if ($value !== '' && $value !== null) {
                    $filteredData[$key] = $value;
                }
            }
            
            if (empty($filteredData)) {
                return [
                    'success' => true,
                    'entity' => $existingEntity,
                    'errors' => []
                ];
            }
            
            $entityClass = get_class($existingEntity);
            $context = [AbstractNormalizer::OBJECT_TO_POPULATE => $existingEntity];
            
            $filteredJson = json_encode($filteredData);
            $entity = $this->serializer->deserialize(
                $filteredJson, 
                $entityClass, 
                'json',
                $context
            );

            $errors = $this->validator->validate($entity);
            
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }
                
                return [
                    'success' => false,
                    'entity' => $existingEntity,
                    'errors' => $errorMessages
                ];
            }
            
            $this->entityManager->flush();
            
            return [
                'success' => true,
                'entity' => $entity,
                'errors' => []
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'entity' => $existingEntity,
                'errors' => ['message' => $e->getMessage()]
            ];
        }
    }

    /**
     * Apply default values to an entity based on its metadata
     *
     * @param object $entity EntitY to apply default values to
     * @param array $jsonData JSON data used to determine which fields are missing
     * @return object The entity with default values applied
     */
    private function applyDefaultValues(object $entity, array $jsonData): object
    {
        $entityClass = get_class($entity);
        $metadata = $this->entityManager->getClassMetadata($entityClass);
        
        foreach ($metadata->getFieldNames() as $fieldName) {
            if ($fieldName === 'id') {
                continue;
            }
            
            if (!array_key_exists($fieldName, $jsonData)) {
                $setterMethod = 'set' . ucfirst($fieldName);
                
                if (method_exists($entity, $setterMethod)) {
                    $type = $metadata->getTypeOfField($fieldName);
                    
                    switch ($type) {
                        case 'string':
                            $entity->$setterMethod('');
                            break;
                        case 'integer':
                        case 'smallint':
                        case 'bigint':
                            $entity->$setterMethod(0);
                            break;
                        case 'boolean':
                            $entity->$setterMethod(false);
                            break;
                        case 'float':
                        case 'decimal':
                            $entity->$setterMethod(0.0);
                            break;
                        case 'array':
                        case 'json':
                            $entity->$setterMethod([]);
                            break;
                        //  Other case keep their default value
                    }
                }
            }        }        
        return $entity;
    }
      /**
     * Process embedded entities in the data
     *
     * @param array $entityData Data containing embedded entities
     * @param string $entityClass Class name of the parent entity
     * @return array The processed data with correctly handled embedded entities
     */
    private function processEmbeddedEntities(array $entityData, string $entityClass): array
    {
        $metadata = $this->entityManager->getClassMetadata($entityClass);
        $associatedEntities = [];

        foreach ($metadata->getAssociationNames() as $associationName) {
            if (isset($entityData[$associationName]) && is_array($entityData[$associationName])) {
                $associationMapping = $metadata->getAssociationMapping($associationName);
                $targetEntityClass = $associationMapping['targetEntity'];
                $associatedData = $entityData[$associationName];

                $associatedData = $this->completeEntityFields($targetEntityClass, $associatedData);

                try {
                    $associatedJson = json_encode($associatedData);
                    $associatedResult = $this->createEntityFromJson($associatedJson, $targetEntityClass);

                    if ($associatedResult['success']) {
                        $associatedEntity = $associatedResult['entity'];

                        // Remplace dans les data avec l'objet (non l’ID)
                        $entityData[$associationName] = $associatedEntity;

                        // Stocke pour lier plus tard
                        $associatedEntities[$associationName] = $associatedEntity;
                    } else {
                        $entityData[$associationName] = null;
                    }
                } catch (\Exception $e) {
                    $entityData[$associationName] = null;
                }
            }
        }

        return [
            'data' => $entityData,
            'associations' => $associatedEntities
        ];
    }
   
    /**
     * Complete all required fields for an entity type with default values
     *
     * @param string $entityClass Class name of the entity
     * @param array $data Data provided for the entity
     * @return array The completed entity data
     */
    private function completeEntityFields(string $entityClass, array $data): array
    {
        $metadata = $this->entityManager->getClassMetadata($entityClass);
        $today = new \DateTime();
        
        foreach ($metadata->getFieldNames() as $fieldName) {
            if ($fieldName === 'id') {
                continue;
            }
            
            if (!array_key_exists($fieldName, $data)) {
                $type = $metadata->getTypeOfField($fieldName);
                switch ($type) {
                    case 'string':
                        $data[$fieldName] = 'default_' . $fieldName;
                        break;
                    case 'integer':
                    case 'smallint':
                    case 'bigint':
                        $data[$fieldName] = 0;
                        break;
                    case 'boolean':
                        $data[$fieldName] = false;
                        break;
                    case 'float':
                    case 'decimal':
                        $data[$fieldName] = 0.0;
                        break;
                    case 'date':
                    case 'datetime':
                        $data[$fieldName] = $today->format('Y-m-d\TH:i:s\Z');
                        break;
                    case 'array':
                    case 'json':
                        $data[$fieldName] = [];
                        break;
                }
            }
        }
        
        return $data;
    }

    /**
     * Complete required fields with default values based on entity metadata
     *
     * @param array $entityData Data provided for the entity
     * @param string $entityClass Class name of the entity
     * @return array The completed entity data with defaults for missing required fields
     */
    private function completeRequiredFields(array $entityData, string $entityClass): array
    {
        $metadata = $this->entityManager->getClassMetadata($entityClass);
        $today = new \DateTime();
        
        foreach ($metadata->getFieldNames() as $fieldName) {
            if ($fieldName === 'id') {
                continue;
            }
            
            if (array_key_exists($fieldName, $entityData)) {
                continue;
            }
            
            $type = $metadata->getTypeOfField($fieldName);
            switch ($type) {
                case 'string':
                    $entityData[$fieldName] = '';
                    break;
                case 'integer':
                case 'smallint':
                case 'bigint':
                    $entityData[$fieldName] = 0;
                    break;
                case 'boolean':
                    $entityData[$fieldName] = false;
                    break;
                case 'float':
                case 'decimal':
                    $entityData[$fieldName] = 0.0;
                    break;
                case 'date':
                case 'datetime':
                    // Format in ISO 8601
                    $entityData[$fieldName] = $today->format('Y-m-d\TH:i:s\Z');
                    break;
                case 'array':
                case 'json':
                    $entityData[$fieldName] = [];
                    break;
            }
        }
          // Handle associations (relations)
        foreach ($metadata->getAssociationNames() as $associationName) {
            if (!array_key_exists($associationName, $entityData)) {
                if ($metadata->isCollectionValuedAssociation($associationName)) {
                    // For collections (ManyToMany, OneToMany), initialize as an empty array
                    $entityData[$associationName] = [];
                } else if (!$metadata->isAssociationInverseSide($associationName)) {
                    // For single associations (ManyToOne, OneToOne), initialize as null
                    $entityData[$associationName] = null;
                }
            }
        }
        
        return $entityData;
    }
    
    /**
     * Maps field names from JSON to entity property names
     * 
     * @param array $data Raw data from JSON
     * @param string $entityClass Entity class name
     * @return array Data with mapped field names
     */
    private function mapFieldNames(array $data, string $entityClass): array
    {
        // Define field name mappings for specific entities
        $fieldMappings = [
            'App\\Entity\\Person' => [
                'adress' => 'adress',
                'firstName' => 'firstname',
                'lastName' => 'lastname',
            ]
        ];
        
        // Check if we have mappings for this entity
        if (isset($fieldMappings[$entityClass])) {
            foreach ($fieldMappings[$entityClass] as $jsonField => $entityField) {
                // If the JSON field exists but the entity field doesn't
                if (isset($data[$jsonField]) && !isset($data[$entityField])) {
                    $data[$entityField] = $data[$jsonField];
                    unset($data[$jsonField]);
                }
            }
        }
        
        return $data;
    }

    /**
     * Process a JSON containing multiple entities and their attributes
     * Creates or updates entities based on whether they already exist
     *
     * @param string $jsonData JSON data containing entities to create/update
     * @param array $entityConfigs Array of entity configurations
     *              Each config should contain:
     *              - 'class': FQCN of the entity
     *              - 'repository': Repository service to find existing entities
     *              - 'identifier': Field name used to identify existing entities (default: 'id') 
     * @return array Array containing the processed results for each entity
     */
    public function processEntities(string $jsonData, array $entityConfigs): array
    {
        $decodedData = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedData)) {
            return [
                'success' => false,
                'message' => 'JSON invalide: ' . json_last_error_msg(),
                'results' => []
            ];
        }

        $results = [];

        foreach ($entityConfigs as $entityType => $config) {
            if (!isset($decodedData[$entityType])) {
                continue;
            }

            $entityClass = $config['class'];
            $repository = $config['repository'];
            $identifierField = $config['identifier'] ?? 'id';

            $entityResults = [];
            $entitiesData = is_array($decodedData[$entityType]) ? $decodedData[$entityType] : [$decodedData[$entityType]];

            foreach ($entitiesData as $entityData) {
                try {
                    $embeddedResult = $this->processEmbeddedEntities($entityData, $entityClass);
                    $entityData = $embeddedResult['data'];
                    $associations = $embeddedResult['associations'];

                    $entityData = $this->completeRequiredFields($entityData, $entityClass);

                    $metadata = $this->entityManager->getClassMetadata($entityClass);
                    foreach ($metadata->getAssociationNames() as $associationName) {
                        if ($metadata->isCollectionValuedAssociation($associationName) && !isset($entityData[$associationName])) {
                            $entityData[$associationName] = [];
                        }
                    }

                    $entityJson = json_encode($entityData);

                    $existingEntity = null;
                    if (isset($entityData[$identifierField])) {
                        $criteria = [$identifierField => $entityData[$identifierField]];
                        $existingEntity = $repository->findOneBy($criteria);
                    }

                    if ($existingEntity) {
                        $result = $this->updateEntityFromJson($entityJson, $existingEntity);
                        $entity = $result['entity'] ?? null;
                    } else {
                        $result = $this->createEntityFromJson($entityJson, $entityClass);
                        $entity = $result['entity'] ?? null;
                    }

                    if ($result['success'] && $entity) {
                        foreach ($associations as $associationName => $associatedEntity) {
                            $setter = 'set' . ucfirst($associationName);
                            if (method_exists($entity, $setter)) {
                                $entity->$setter($associatedEntity);
                            }
                        }

                        $this->entityManager->persist($entity);
                    }

                } catch (\Exception $e) {
                    $result = [
                        'success' => false,
                        'entity' => null,
                        'errors' => ['message' => 'Erreur de traitement: ' . $e->getMessage()]
                    ];
                }

                $entityResults[] = [
                    'data' => $entityData,
                    'result' => $result
                ];
            }

            $results[$entityType] = $entityResults;
        }

        try {
            $this->entityManager->flush();
            return [
                'success' => true,
                'message' => 'Entités traitées avec succès',
                'results' => $results
            ];
        } catch (\Exception $e) {
            if (!$this->entityManager->isOpen()) {
                $this->entityManager->clear();
            }
            return [
                'success' => false,
                'message' => 'Erreur lors de la persistance des entités: ' . $e->getMessage(),
                'results' => $results
            ];
        }
    }
}