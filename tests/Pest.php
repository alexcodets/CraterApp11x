<?php

use Crater\Traits\FastMigrationTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
uses(TestCase::class, RefreshDatabase::class)->in('Unit');
uses(TestCase::class, FastMigrationTrait::class)->in('Services');
uses(TestCase::class, FastMigrationTrait::class)->in('Http');
uses(TestCase::class, FastMigrationTrait::class)->in('Console');
uses(TestCase::class, FastMigrationTrait::class)->in('Jobs');
