# Herd ENT - Development Guide

## Architecture Overview

Herd ENT is built with Laravel and uses Inertia.js with Vue.js for the frontend. This creates a single-page application feel while leveraging Laravel's backend capabilities.

### Key Technologies

- **Backend**: Laravel 10+
- **Frontend**: Vue.js 3 with Inertia.js
- **CSS Framework**: Tailwind CSS
- **Authentication**: Laravel Breeze
- **Authorization**: Spatie Permission
- **Database**: MySQL/PostgreSQL
- **Icons**: Heroicons
- **Charts**: Chart.js

## Directory Structure

```
app/
├── Http/
│   ├── Controllers/     # Request handlers
│   ├── Middleware/      # Request filters
│   └── Requests/        # Form validations
├── Models/              # Database models and relationships
├── Policies/            # Authorization policies
├── Providers/           # Service providers
└── Services/            # Business logic services

resources/
├── js/
│   ├── Components/      # Reusable Vue components
│   ├── Layouts/         # Page layouts
│   └── Pages/           # Page-specific components
│       ├── Auth/
│       ├── Patients/
│       ├── Appointments/
│       ├── Encounters/
│       ├── Invoices/
│       └── ...
├── css/                 # Stylesheets
└── views/               # Blade templates (minimal usage with Inertia)

routes/
├── web.php              # Web routes
├── api.php              # API routes
└── auth.php             # Authentication routes
```

## Adding New Features

### 1. Create Database Migrations

```bash
php artisan make:migration create_feature_name_table
```

Edit the migration file in `database/migrations/`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feature_name', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Add your columns here
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feature_name');
    }
};
```

### 2. Create Model

```bash
php artisan make:model FeatureName
```

Edit the model in `app/Models/FeatureName.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureName extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        // Add your fillable fields
    ];

    // Define relationships
    public function otherModel()
    {
        return $this->belongsTo(OtherModel::class);
    }
}
```

### 3. Create Controller

```bash
php artisan make:controller FeatureNameController --resource
```

Edit the controller in `app/Http/Controllers/FeatureNameController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\FeatureName;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FeatureNameController extends Controller
{
    public function index()
    {
        $items = FeatureName::all();
        
        return Inertia::render('FeatureNames/Index', [
            'items' => $items
        ]);
    }

    public function create()
    {
        return Inertia::render('FeatureNames/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Add validations for your fields
        ]);

        FeatureName::create($validated);

        return redirect()->route('feature-names.index')
            ->with('message', 'Feature created successfully');
    }

    // Implement other resource methods (show, edit, update, destroy)
}
```

### 4. Add Routes

Edit `routes/web.php` to add your feature routes:

```php
Route::middleware(['auth', 'permission:view feature-names'])->group(function () {
    Route::resource('feature-names', FeatureNameController::class);
});
```

### 5. Create Vue Components

Create the required Vue components in `resources/js/Pages/FeatureNames/`:

- `Index.vue`: List view of the feature items
- `Create.vue`: Form for creating new items
- `Edit.vue`: Form for editing existing items
- `Show.vue`: Detailed view of a single item

Example `Index.vue`:

```vue
<template>
  <AppLayout title="Feature Names">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Feature Names
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Your content here -->
            <div class="flex justify-between mb-6">
              <h3 class="text-lg font-medium">List of Features</h3>
              <Link
                v-if="$page.props.auth.user.permissions.includes('create feature-names')"
                :href="route('feature-names.create')"
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
              >
                Add New
              </Link>
            </div>

            <!-- Table of features -->
            <table class="min-w-full divide-y divide-gray-200">
              <!-- Table headers and rows -->
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
  items: Array
});
</script>
```

### 6. Add Permissions

Add appropriate permissions to the database seeder:

```php
// In database/seeders/PermissionSeeder.php

Permission::create(['name' => 'view feature-names']);
Permission::create(['name' => 'create feature-names']);
Permission::create(['name' => 'edit feature-names']);
Permission::create(['name' => 'delete feature-names']);

// Assign to roles
$adminRole = Role::findByName('administrator');
$adminRole->givePermissionTo([
    'view feature-names',
    'create feature-names',
    'edit feature-names',
    'delete feature-names'
]);
```

Run the seeder:

```bash
php artisan db:seed --class=PermissionSeeder
```

## Testing

### Creating Tests

```bash
php artisan make:test FeatureNameTest
```

Edit the test in `tests/Feature/FeatureNameTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FeatureName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_feature_names()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view feature-names');

        $response = $this->actingAs($user)
            ->get(route('feature-names.index'));

        $response->assertStatus(200);
    }

    // Add more tests for create, edit, delete, etc.
}
```

### Running Tests

```bash
php artisan test
```

## Best Practices

1. **Use Form Requests** for validation
2. **Follow SOLID principles**
3. **Use policies** for authorization
4. **Write tests** for all features
5. **Comment your code** where necessary
6. **Follow Laravel's conventions** for naming and structure
7. **Use Laravel's built-in methods** instead of writing custom code
8. **Keep controllers thin** by moving business logic to services

## Common Patterns

### Service Classes

For complex business logic, create service classes:

```php
<?php

namespace App\Services;

use App\Models\FeatureName;

class FeatureNameService
{
    public function process(array $data): FeatureName
    {
        // Complex business logic here
        return FeatureName::create([
            'name' => $data['name'],
            // Process more fields
        ]);
    }
}
```

### Form Requests

For complex validation:

```bash
php artisan make:request StoreFeatureNameRequest
```

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeatureNameRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create feature-names');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            // More validation rules
        ];
    }
}
```

## Troubleshooting Development Issues

1. **Clear Laravel Cache**
   ```bash
   php artisan optimize:clear
   ```

2. **Rebuild Node Modules**
   ```bash
   rm -rf node_modules
   npm install
   ```

3. **Check Logs**
   - Laravel logs: `storage/logs/laravel.log`
   - PHP error logs: Check your web server's error log

4. **Debug with Telescope**
   Laravel Telescope provides insights into your application's requests, exceptions, logs, database queries, etc.

5. **Use dd() or dump()**
   ```php
   dd($variable); // "Dump and Die"
   dump($variable); // Continue execution after dumping
   ``` 