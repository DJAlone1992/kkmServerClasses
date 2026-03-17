<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PostRector\Rector\DocblockNameImportingPostRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnArrayDocblockBasedOnArrayMapRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\AddReturnArrayDocblockFromDataProviderParamRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\AddReturnDocblockDataProviderRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\DocblockVarArrayFromGetterReturnRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\DocblockVarArrayFromPropertyDefaultsRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\DocblockVarFromParamDocblockInConstructorRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddReturnDocblockForArrayDimAssignedObjectRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddReturnDocblockForJsonArrayRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\DocblockGetterReturnArrayFromPropertyDocblockVarRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\DocblockReturnArrayFromDirectArrayInstanceRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/frontend',
        __DIR__ . '/backend',
    ])
    ->withRules([
        AddReturnArrayDocblockBasedOnArrayMapRector::class,
        AddReturnArrayDocblockFromDataProviderParamRector::class,
        AddReturnDocblockDataProviderRector::class,
        AddReturnDocblockForArrayDimAssignedObjectRector::class,
        AddReturnDocblockForJsonArrayRector::class,
        DocblockVarArrayFromGetterReturnRector::class,
        DocblockGetterReturnArrayFromPropertyDocblockVarRector::class,
        //DocblockNameImportingPostRector::class,
        DocblockReturnArrayFromDirectArrayInstanceRector::class,
        DocblockVarArrayFromPropertyDefaultsRector::class,
        DocblockVarFromParamDocblockInConstructorRector::class,
        AddReturnDocblockDataProviderRector::class,
        AddReturnDocblockForArrayDimAssignedObjectRector::class,
        AddReturnDocblockForJsonArrayRector::class,
        DocblockVarArrayFromGetterReturnRector::class,
        DocblockGetterReturnArrayFromPropertyDocblockVarRector::class,
        //DocblockNameImportingPostRector::class,
        DocblockReturnArrayFromDirectArrayInstanceRector::class,
        DocblockVarArrayFromPropertyDefaultsRector::class,
        DocblockVarFromParamDocblockInConstructorRector::class,
    ])
    ->withDowngradeSets(php74: true)
    // uncomment to reach your current PHP version
    ->withPhpSets(php74: true)
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(0);
