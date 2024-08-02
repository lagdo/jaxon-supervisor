<?php

namespace Lagdo\Supervisor\App\Ui;

use Lagdo\Supervisor\App\Ajax\Web\Home;
use Lagdo\Supervisor\App\Ajax\Web\Process;
use Lagdo\Supervisor\App\Ajax\Web\Server;
use Lagdo\UiBuilder\Builder;
use Lagdo\UiBuilder\Jaxon\Builder as JaxonBuilder;
use Supervisor\Process as SupervisorProcess;

use function Jaxon\cl;
use function Jaxon\js;
use function Jaxon\rq;

class UiBuilder implements UiBuilderInterface
{
    /**
     * @return string
     */
    public function wrapper()
    {
        $rqHome = rq(Home::class);
        $ui = JaxonBuilder::make();
        $ui
            ->div()
                ->row()
                    ->col(12)
                        ->panel()
                            ->panelBody()
                                ->div()
                                    ->setClass('pull-left jaxon-supervisor-refresh-btn jaxon-supervisor-refresh-disabled')
                                    ->button(Builder::BTN_SMALL + Builder::BTN_PRIMARY)
                                        ->jxnClick(js('jaxon.supervisor')->enableRefresh())
                                        ->span()
                                            ->setClass('glyphicon glyphicon-play')
                                        ->end()
                                    ->end()
                                ->end()
                                ->div()
                                    ->setClass('pull-left jaxon-supervisor-refresh-btn jaxon-supervisor-refresh-enabled')
                                    ->button(Builder::BTN_SMALL + Builder::BTN_DANGER)
                                        ->jxnClick(js('jaxon.supervisor')->disableRefresh())
                                        ->span()
                                            ->setClass('glyphicon glyphicon-stop')
                                        ->end()
                                    ->end()
                                ->end()
                                ->div()
                                    ->setClass('pull-left jaxon-supervisor-refresh-btn')
                                    ->button(Builder::BTN_SMALL + Builder::BTN_PRIMARY)
                                        ->jxnClick($rqHome->refresh(false))
                                        ->span()
                                            ->setClass('glyphicon glyphicon-refresh')
                                        ->end()
                                        ->addText(' Refresh (')
                                        ->span()
                                            ->setId('jaxon-supervisor-refresh-countdown')
                                            ->addText('0')
                                        ->end()
                                        ->addText(')')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->row()
                    ->jxnShow($rqHome)
                    ->jxnHtml($rqHome)
                ->end()
            ->end();
        return $ui->build();
    }

    /**
     * @param array $serverItemIds
     *
     * @return string
     */
    public function servers(array $serverItemIds)
    {
        $rqServer = rq(Server::class);
        $ui = JaxonBuilder::make();
        foreach($serverItemIds as $serverItemId)
        {
            $ui
                ->col(6, ['class' => 'col-sm-12'])/*->colSm(12)*/->jxnShow($rqServer, $serverItemId)
                ->end();
        }
        return $ui->build();
    }

    /**
     * Get the HTML code of a simple form
     *
     * @param string $serverName
     * @param string $serverVersion
     *
     * @return string
     */
    public function server(string $server, string $serverName, string $serverVersion, array $processes)
    {
        $rqServer = rq(Server::class);
        $rqProcess = rq(Process::class);
        $clProcess = cl(Process::class);
        $ui = JaxonBuilder::make();
        $ui
            ->panel()
                ->panelHeader()
                    ->row()
                        ->col(5)
                            ->addHtml($serverName . '&nbsp;' . $serverVersion)
                        ->end()
                        ->col(7)
                            ->div(['class' => 'pull-right', 'style' => 'padding-left:5px;'])/*->pull('right')*/
                                ->button(Builder::BTN_SMALL + Builder::BTN_DANGER)
                                    ->jxnClick($rqServer->stop($server))
                                    ->span()->setClass('glyphicon glyphicon-stop')
                                    ->end()
                                    ->addHtml('&nbsp;Stop all')
                                ->end()
                            ->end()
                            ->div(['class' => 'pull-right', 'style' => 'padding-left:5px;'])/*->pull('right')*/
                                ->button(Builder::BTN_SMALL + Builder::BTN_PRIMARY)
                                    ->jxnClick($rqServer->start($server))
                                    ->span()->setClass('glyphicon glyphicon-play')
                                    ->end()
                                    ->addHtml('&nbsp;Start all')
                                ->end()
                            ->end()
                            ->div(['class' => 'pull-right', 'style' => 'padding-left:5px;'])/*->pull('right')*/
                                ->button(Builder::BTN_SMALL + Builder::BTN_PRIMARY)
                                    ->jxnClick($rqServer->restart($server))
                                    ->span()->setClass('glyphicon glyphicon-repeat')
                                    ->end()
                                    ->addHtml('&nbsp;Restart all')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->panelBody(['style' => 'padding:5px 15px;']);
        foreach($processes as $process)
        {
            $clProcess->setProcess($process);
            $ui
                    ->div(['style' => 'margin:5px 0;'])
                        ->jxnShow($rqProcess, $clProcess->getItemId())
                        ->addHtml($clProcess->html())
                    ->end();
        }
        $ui
                ->end()
            ->end();
        return $ui->build();
    }

    /**
     * @param SupervisorProcess $process
     *
     * @return string
     */
    public function process(string $server, SupervisorProcess $process)
    {
        $rqProcess = rq(Process::class);
        $processId = $process['id'];
        $ui = JaxonBuilder::make();
        $ui
            ->row()
                ->col(5)
                    ->addText($process['id'])
                ->end()
                ->col(2, ['style' => 'text-align:center;'])
                    ->h5(['style' => 'margin:0;'])
                        ->span(['class' => 'label label-' . ($process->isRunning() ? 'success' : 'default')])
                            ->addText($process['statename'])
                        ->end()
                    ->end()
                ->end()
                ->col(3, ['style' => 'text-align:center;'])
                    ->addText($process['uptime'])
                ->end()
                ->col(2);
        if($process->isRunning())
        {
            $ui
                    ->div(['class' => 'pull-right', 'style' => 'padding-left:5px;'])/*->pull('right')*/
                        ->button(Builder::BTN_SMALL + Builder::BTN_DANGER, ['class' => 'btn-stop'])
                            ->jxnClick($rqProcess->stop($server, $processId))
                            ->span()->setClass('glyphicon glyphicon-stop')
                            ->end()
                        ->end()
                    ->end()
                    ->div(['class' => 'pull-right', 'style' => 'padding-left:5px;'])/*->pull('right')*/
                        ->button(Builder::BTN_SMALL + Builder::BTN_PRIMARY, ['class' => 'btn-restart'])
                            ->jxnClick($rqProcess->restart($server, $processId))
                            ->span()->setClass('glyphicon glyphicon-repeat')
                            ->end()
                        ->end()
                    ->end();
        }
        else
        {
            $ui
                    ->div(['class' => 'pull-right', 'style' => 'padding-left:5px;'])/*->pull('right')*/
                        ->button(Builder::BTN_SMALL + Builder::BTN_PRIMARY, ['class' => 'btn-start'])
                            ->jxnClick($rqProcess->start($server, $processId))
                            ->span()->setClass('glyphicon glyphicon-play')
                            ->end()
                        ->end()
                    ->end();
        }
        $ui
                ->end()
            ->end();
        return $ui->build();
    }
}
