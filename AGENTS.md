# AGENTS.md - fskblog Development Guidelines

## Commands

### Testing
```bash
composer test              # Run full test suite (PHPUnit)
php artisan test           # Run all tests
php artisan test --filter=PostTest        # Run single test class
php artisan test tests/Feature/PostTest.php:42  # Run specific test method
php artisan test --parallel               # Run tests in parallel
```

### Linting & Formatting
```bash
vendor/bin/pint            # Auto-format PHP (Laravel Pint)
vendor/bin/pint --test     # Check formatting without changes
```

### Frontend
```bash
npm run dev                # Start Vite dev server (HMR)
npm run build              # Production build
```

### Development
```bash
composer run dev           # Full stack: server, queue, logs, vite
php artisan serve          # Start PHP dev server only
php artisan migrate:fresh --seed  # Reset DB with seed data
```

## Code Style

### PHP (Laravel 13)
- **Formatting**: Follow Laravel Pint defaults (PSR-12). 4 spaces, LF line endings.
- **Imports**: Group by namespace, alphabetize within groups. Use full class names in type hints.
- **Types**: Always declare return types. Use `Builder`, `BelongsTo`, `HasMany` for Eloquent.
- **Naming**: Controllers: `PostController`. Models: singular `Post`. Policies: `PostPolicy`. Methods: camelCase.
- **Validation**: Use array syntax `$request->validate([...])` in controllers.
- **Error Handling**: Let Laravel handle exceptions. Use `abort(404)` for missing resources. Return validation errors via `$request->validate()`.
- **Caching**: Use `Cache::remember()` with explicit keys. Invalidate with `Cache::forget()` after mutations.
- **Queries**: Always use `select()` for needed columns. Constrain eager loading: `with(['category:id,name,slug'])`.
- **Authorization**: Use Policies via `$this->authorize('update', $post)`. Check `$user->is_admin` in policy methods.

### Vue 3 (Inertia.js)
- **Syntax**: Use `<script setup>` with Composition API in all components.
- **Imports**: Use `@/` alias for `resources/js/`. Import from `@inertiajs/vue3` for Inertia utilities.
- **Props**: Define with `defineProps({})` using Object type syntax.
- **Naming**: PascalCase components. CamelCase methods/variables.
- **Styling**: TailwindCSS v4 utility classes. Support dark mode with `dark:` prefix.
- **Routing**: Use Ziggy `route()` helper for all URLs. Use `<Link>` for navigation, `router.delete()` for destructive actions.
- **Icons**: Use `lucide-vue-next` components.

## Architecture

### Structure
```
app/
├── Http/Controllers/       # Public controllers
│   └── Admin/              # Admin CRUD controllers
├── Models/                 # Eloquent models (Post, Comment, Category, Tag, User)
├── Policies/               # Authorization policies
└── Providers/              # Service providers
resources/js/
├── Pages/                  # Inertia pages (mirror route structure)
├── Components/             # Reusable Vue components
└── Layouts/                # AppLayout, AdminLayout, etc.
```

### Key Patterns
- **Public routes**: Read-only, cached, use query scopes (`published()`, `featured()`).
- **Admin routes**: Full CRUD, protected by `admin` middleware, flash messages in Turkish.
- **Models**: Use `$fillable`, `casts()`, `boot()` events, Laravel 10+ `Attribute` accessors.
- **Policies**: Standard methods (`viewAny`, `view`, `create`, `update`, `delete`). Return booleans.

## Security
- `SecurityHeaders` middleware adds CSP, HSTS, X-Frame-Options, etc.
- Policies enforce resource-level authorization.
- Search endpoint rate-limited (30 req/min).
- Session-based view tracking prevents count inflation.
