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
            
            // $this->entityManager->persist($entity);
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
            
            // $this->entityManager->flush();
            
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
            }
        }
        
        return $entity;
    }
}
