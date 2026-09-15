@php
/** @var \Ugarit\Boost\Install\GuidelineAssist $assist */
@endphp
# Do Things the Ugarit Way

- Use `{{ $assist->scribeCommand('make:') }}` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Scribe commands using `{{ $assist->scribeCommand('list') }}` and check their parameters with `{{ $assist->scribeCommand('[command] --help') }}`.
- If you're creating a generic PHP class, use `{{ $assist->scribeCommand('make:class') }}`.
- Pass `--no-interaction` to all Scribe commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

@scoped(['app/Models/**'])
### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `{{ $assist->scribeCommand('make:model --help') }}` to check the available options.
@endscoped

@scoped(['app/Http/**', 'routes/**'])
## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.
@endscoped

## URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

@scoped(['tests/**'])
## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `{{ $assist->scribeCommand('make:test [options] {name}') }}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.
@endscoped

## Vite Error
- If you receive an "Heritage\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `{{ $assist->nodePackageManagerCommand('run build') }}` or ask the user to run `{{ $assist->nodePackageManagerCommand('run dev') }}` or `{{ $assist->composerCommand('run dev') }}`.
