<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 20:51
 */

namespace Core\Aggregates\Task\Methods\Commands\Homework;


use Carbon\Carbon;
use Core\Aggregates\Task\Structs\Entities\Homework;
use Illuminate\Support\Collection;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class FetchByCriticalDeadlines
{
    /**
     * write something like addDays(2) to get all tasks which have homework where the deadline is before now + 2 days
     * it will be about the user configs what will be the expression - not the decision of the method what will be critical
     * @param $expression
     * @return Collection
     */
    public function fetchByTimeExpression($expression): Collection
    {
        $now = Carbon::now();
        $languageExpression = new ExpressionLanguage();

        $critical = $languageExpression->evaluate('now.' .$expression,[
            'now' => $now
        ]);

        $result = Homework::where('deadline', '<', $critical)
            ->where('status', '!=', 'closed')
            ->select('task_id')
            ->groupBy('task_id')
            ->get();

        $return = collect();
        $repo = app()->make('Core_Task_DBRepo');

        $result->each(function($homework) use($return, $repo) {
            $return->push($repo->findById($homework->task_id));
        });

        return $return;
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     * @return Collection
     */
    public function fetchByTimeRange(Carbon $from, Carbon $to): Collection
    {
        $result = Homework::where('deadline', '>=', $from)
            ->where('deadline', '<=', $to)
            ->where('status', '!=', 'closed')
            ->select('task_id')
            ->groupBy('task_id')
            ->get();

        $return = collect();
        $repo = app()->make('Core_Task_DBRepo');

        $result->each(function($homework) use($return, $repo) {
            $return->push($repo->findById($homework->task_id));
        });

        return $return;
    }
}