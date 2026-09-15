<?php

declare(strict_types=1);

namespace Ugarit\Boost\Console;

use Heritage\Console\Command;
use Heritage\Support\Collection;
use Ugarit\Boost\Concerns\DisplayHelper;
use Ugarit\Boost\Install\SkillComposer;

use function Ugarit\Prompts\note;
use function Ugarit\Prompts\table;

class ListSkillCommand extends Command
{
    use DisplayHelper;

    protected $signature = 'boost:list-skills';

    protected $description = 'List all available skills in the current project';

    public function handle(SkillComposer $skillComposer): int
    {
        $skills = $skillComposer->skills();

        if ($skills->isEmpty()) {
            $this->info('No skills available in this project.');

            return self::SUCCESS;
        }

        $this->displayBoostHeader('Skills', config('app.name'));

        $count = $skills->count();
        note("Found {$count} skill".($count === 1 ? '' : 's'));

        $this->displaySkillsTable($skills);

        return self::SUCCESS;
    }

    protected function displaySkillsTable(Collection $skills): void
    {
        $rows = $skills
            ->sortBy(fn ($skill) => $skill->name)
            ->map(fn ($skill): array => $skill->custom
                ? [$this->dim($skill->name.'*'), $this->yellow('local')]
                : [$skill->name, $this->dim($skill->package)]
            )
            ->values()
            ->toArray();

        table(
            headers: ['Skill', 'Source'],
            rows: $rows
        );
    }
}
