<?php

use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;

uses(
    Tests\TestCase::class,
    //Illuminate\Foundation\Testing\RefreshDatabase::class,
    FastRefreshDatabase::class,
)->in(__DIR__);
