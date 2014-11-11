<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Goals\Metrics\GoalSpecific;

use Piwik\DataTable\Row;
use Piwik\Piwik;
use Piwik\Plugins\Goals\Metrics\GoalSpecificProcessedMetric;
use Piwik\Tracker\GoalManager;

/**
 * The conversion rate for a specific goal. Calculated as:
 *
 *     goal's nb_conversions / nb_visits
 *
 * The goal's nb_conversions is calculated by the Goal archiver and nb_visits
 * by the core archiving process.
 */
class ConversionRate extends GoalSpecificProcessedMetric
{
    public function getName()
    {
        return $this->getColumnPrefix() . '_conversion_rate';
    }

    public function getTranslatedName()
    {
        return self::getName(); // TODO???
    }

    public function getDependentMetrics()
    {
        return array('goals');
    }

    public function format($value)
    {
        return ($value * 100) . '%';
    }

    public function compute(Row $row)
    {
        $goalMetrics = $this->getGoalMetrics($row);

        $nbVisits = $this->getMetric($row, 'nb_visits');
        $conversions = $this->getMetric($goalMetrics, 'nb_conversions');

        return Piwik::getQuotientSafe($conversions, $nbVisits, GoalManager::REVENUE_PRECISION + 2);
    }
}