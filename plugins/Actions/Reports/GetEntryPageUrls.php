<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\Actions\Reports;

use Piwik\Common;
use Piwik\Piwik;
use Piwik\Plugin\ViewDataTable;
use Piwik\API\Request;
use Piwik\Plugins\Actions\Columns\EntryPageUrl;
use Piwik\Plugins\Actions\Metrics\AveragePageGenerationTime;
use Piwik\Plugins\Actions\Metrics\AverageTimeOnPage;
use Piwik\Plugins\Actions\Metrics\BounceRate;
use Piwik\Plugins\Actions\Metrics\ExitRate;

class GetEntryPageUrls extends Base
{
    protected function init()
    {
        parent::init();

        $this->dimension     = new EntryPageUrl();
        $this->name          = Piwik::translate('Actions_SubmenuPagesEntry');
        $this->documentation = Piwik::translate('Actions_EntryPagesReportDocumentation', '<br />')
                             . '<br />' . Piwik::translate('General_UsePlusMinusIconsDocumentation');

        $this->metrics = array('entry_nb_visits', 'entry_bounce_count');
        $this->processedMetrics = array(
            new AverageTimeOnPage(),
            new BounceRate(),
            new ExitRate(),
            new AveragePageGenerationTime()
        );
        $this->order   = 3;

        $this->actionToLoadSubTables = $this->action;

        $this->menuTitle   = 'Actions_SubmenuPagesEntry';
        $this->widgetTitle = 'Actions_WidgetPagesEntry';
    }

    public function getProcessedMetrics()
    {
        $result = parent::getProcessedMetrics();

        // these metrics are not displayed in the API.getProcessedReport version of this report,
        // so they are removed here.
        unset($result['avg_time_on_page']);
        unset($result['exit_rate']);

        return $result;
    }

    protected function getMetricsDocumentation()
    {
        $metrics = parent::getMetricsDocumentation();
        $metrics['bounce_rate'] = Piwik::translate('General_ColumnBounceRateForPageDocumentation');

        return $metrics;
    }

    public function configureView(ViewDataTable $view)
    {
        $view->config->addTranslations(array('label' => $this->dimension->getName()));

        $view->config->title = $this->name;
        $view->config->columns_to_display = array('label', 'entry_nb_visits', 'entry_bounce_count', 'bounce_rate');
        $view->requestConfig->filter_sort_column = 'entry_nb_visits';
        $view->requestConfig->filter_sort_order  = 'desc';

        $this->addPageDisplayProperties($view);
        $this->addBaseDisplayProperties($view);
    }

    public function getRelatedReports()
    {
        return array(
            new GetEntryPageTitles()
        );
    }
}
