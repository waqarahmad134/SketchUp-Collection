<?php

/**
 * Dev-only stubs to satisfy static analysis when Filament classes are not indexed.
 * At runtime, real Filament classes are loaded, so these stubs remain unused.
 */

namespace Filament\Forms;

if (!class_exists(Form::class)) {
    class Form
    {
        public function schema(array $components = []): self
        {
            return $this;
        }
    }
}

namespace Filament\Schemas;

if (!class_exists(Schema::class)) {
    class Schema
    {
        public function schema(array $components = []): self
        {
            return $this;
        }
    }
}

namespace Filament\Resources;

if (!class_exists(Resource::class)) {
    class Resource
    {
        public static function form(\Filament\Schemas\Schema $schema)
        {
            return $schema;
        }
    }
}

