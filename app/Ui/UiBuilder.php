<?php

namespace Lagdo\Supervisor\App\Ui;

use Lagdo\Supervisor\App\Ajax\Web\Home;
use Lagdo\Supervisor\App\Ajax\Web\Process;
use Lagdo\Supervisor\App\Ajax\Web\Server;
use Lagdo\UiBuilder\BuilderInterface;
use Supervisor\Process as SupervisorProcess;

use function Jaxon\cl;
use function Jaxon\jo;
use function Jaxon\rq;

class UiBuilder implements UiBuilderInterface
{
    /**
     * @param BuilderInterface $html
     */
    public function __construct(protected BuilderInterface $html)
    {}

    /**
     * @return string
     */
    public function wrapper(): string
    {
        $rqHome = rq(Home::class);
        return $this->html->build(
            $this->html->row(
                $this->html->col(
                    $this->html->panel(
                        $this->html->panelBody(
                            $this->html->div(
                                $this->html->button(
                                    $this->html->span()
                                    ->setClass('glyphicon glyphicon-play')
                                )
                                ->small()->success()
                                ->jxnClick(jo('jaxon.supervisor')->enableRefresh())
                            )
                            ->setClass('pull-left jaxon-supervisor-refresh-btn jaxon-supervisor-refresh-disabled'),
                            $this->html->div(
                                $this->html->button(
                                    $this->html->span()
                                    ->setClass('glyphicon glyphicon-stop')
                                )
                                ->small()->danger()
                                ->jxnClick(jo('jaxon.supervisor')->disableRefresh())
                            )
                            ->setClass('pull-left jaxon-supervisor-refresh-btn jaxon-supervisor-refresh-enabled'),
                            $this->html->div(
                                $this->html->button(
                                    $this->html->span()
                                        ->setClass('glyphicon glyphicon-refresh'),
                                    $this->html->span(' Refresh ('),
                                    $this->html->span('0')
                                        ->setId('jaxon-supervisor-refresh-countdown'),
                                    $this->html->span(')')
                                )
                                ->small()->primary()
                                ->jxnClick($rqHome->refresh(false))
                            )
                            ->setClass('pull-left jaxon-supervisor-refresh-btn')
                        )
                    )
                )
                ->width(12)
            ),
            $this->html->row()
                ->jxnBind($rqHome)
                ->jxnHtml($rqHome)
        );
    }

    /**
     * @param array $serverItemIds
     *
     * @return string
     */
    public function servers(array $serverItemIds): string
    {
        $rqServer = rq(Server::class);
        return $this->html->build(
            $this->html->each($serverItemIds, fn($serverItemId) =>
                $this->html->col()
                    /*->colSm(12)*/
                    ->setClass('col-sm-12')
                    ->width(6)
                    ->jxnBind($rqServer, $serverItemId)
            )
        );
    }

    /**
     * Get the HTML code of a simple form
     *
     * @param string $serverName
     * @param string $serverVersion
     *
     * @return string
     */
    public function server(string $server, string $serverName, string $serverVersion, array $processes): string
    {
        $rqServer = rq(Server::class);
        $rqProcess = rq(Process::class);
        $clProcess = cl(Process::class);
        return $this->html->build(
            $this->html->panel(
                $this->html->panelHeader(
                    $this->html->row(
                        $this->html->col(
                            $this->html->span()
                                ->addHtml($serverName . '&nbsp;' . $serverVersion)
                        )
                        ->width(5),
                        $this->html->col(
                            $this->html->div(
                                ['class' => 'pull-right', 'style' => 'padding-left:5px;'],
                                $this->html->button(
                                    $this->html->span()
                                        ->setClass('glyphicon glyphicon-stop')
                                )
                                ->small()->danger()
                                ->addHtml('&nbsp;Stop all')
                                ->jxnClick($rqServer->stop($server))
                            )
                            /*->pull('right')*/,
                            $this->html->div(
                                ['class' => 'pull-right', 'style' => 'padding-left:5px;'],
                                $this->html->button(
                                    $this->html->span()
                                        ->setClass('glyphicon glyphicon-play')
                                )
                                ->small()->primary()
                                ->addHtml('&nbsp;Start all')
                                ->jxnClick($rqServer->start($server))
                            )
                            /*->pull('right')*/,
                            $this->html->div(
                                ['class' => 'pull-right', 'style' => 'padding-left:5px;'],
                                $this->html->button(
                                    $this->html->span()
                                        ->setClass('glyphicon glyphicon-repeat')
                                )
                                ->small()->primary()
                                ->addHtml('&nbsp;Restart all')
                                ->jxnClick($rqServer->restart($server))
                            )
                            /*->pull('right')*/
                        )
                        ->width(7)
                    )
                ),
                $this->html->panelBody(
                    ['style' => 'padding:5px 15px;'],
                    $this->html->each($processes, function($process) use($rqProcess, $clProcess) {
                        $clProcess->setProcess($process);
                        return $this->html->div(['style' => 'margin:5px 0;'])
                            ->jxnBind($rqProcess, $clProcess->getItemId())
                            ->addHtml($clProcess->html());
                    })
                )
            )
        );
    }

    /**
     * @param SupervisorProcess $process
     *
     * @return string
     */
    public function process(string $server, SupervisorProcess $process): string
    {
        $rqProcess = rq(Process::class);
        $processId = $process['id'];
        $labelClass = 'label label-' . ($process->isRunning() ? 'success' : 'default');
        return $this->html->build(
            $this->html->row(
                $this->html->col(
                    $this->html->span()
                        ->addText((string)$process['id'])
                )
                ->width(5),
                $this->html->col(
                    ['style' => 'text-align:center;'],
                    $this->html->h5(
                        ['style' => 'margin:0;'],
                        $this->html->span(['class' => $labelClass])
                            ->addText((string)$process['statename'])
                    )
                )
                ->width(2),
                $this->html->col(
                    ['style' => 'text-align:center;'],
                    $this->html->span()
                        ->addText((string)$process['uptime'])
                )
                ->width(3),
                $this->html->col(
                    $this->html->when($process->isRunning(), fn() =>
                        $this->html->list(
                            $this->html->div(
                                ['class' => 'pull-right', 'style' => 'padding-left:5px;'],
                                $this->html->button(
                                    ['class' => 'btn-stop'],
                                    $this->html->span(['class' => 'glyphicon glyphicon-stop'])
                                )
                                ->small()->danger()
                                ->jxnClick($rqProcess->stop($server, $processId))
                            )
                            /*->pull('right')*/,
                            $this->html->div(
                                ['class' => 'pull-right', 'style' => 'padding-left:5px;'],
                                $this->html->button(
                                    ['class' => 'btn-restart'],
                                    $this->html->span(['class' => 'glyphicon glyphicon-repeat'])
                                )
                                ->small()->primary()
                                ->jxnClick($rqProcess->restart($server, $processId))
                            )
                            /*->pull('right')*/
                        )
                    ),
                    $this->html->when(!$process->isRunning(), fn() =>
                        $this->html->div(
                            ['class' => 'pull-right', 'style' => 'padding-left:5px;'],
                            $this->html->button(
                                ['class' => 'btn-start'],
                                $this->html->span(['class' => 'glyphicon glyphicon-play'])
                            )
                            ->small()->primary()
                            ->jxnClick($rqProcess->start($server, $processId))
                        )
                        /*->pull('right')*/
                    )
                )
                ->width(2),
            )
        );
    }
}
