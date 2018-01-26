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
}