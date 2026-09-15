@php
/** @var \Ugarit\Boost\Install\GuidelineAssist $assist */
@endphp
@scoped(['tests/**'])
# PHPUnit

- This project uses PHPUnit. Create tests with `{{ $assist->scribeCommand('make:test --phpunit {name}') }}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `{{ $assist->scribeCommand('test --compact') }}`.
- Rerun a test after each change to it.
- Run `{{ $assist->binCommand('phpunit') }}` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
@endscoped
