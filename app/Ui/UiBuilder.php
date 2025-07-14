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
                                    $this->html->i(['class' => 'fa fa-play'])
                                )
                                ->small()->success()
                                ->jxnClick(jo()->jaxon->supervisor->enableRefresh())
                            )
                            ->setStyle('float:left;')
                            ->setClass('jaxon-supervisor-refresh-btn jaxon-supervisor-refresh-disabled'),
                            $this->html->div(
                                $this->html->button(
                                    $this->html->i(['class' => 'fa fa-stop'])
                                )
                                ->small()->danger()
                                ->jxnClick(jo()->jaxon->supervisor->disableRefresh())
                            )
                            ->setStyle('float:left;')
                            ->setClass('jaxon-supervisor-refresh-btn jaxon-supervisor-refresh-enabled'),
                            $this->html->div(
                                $this->html->button(
                                    $this->html->i(['class' => 'fa fa-sync']),
                                    $this->html->span()
                                        ->addHtml('&nbsp;Refresh ('),
                                    $this->html->span('0')
                                        ->setId('jaxon-supervisor-refresh-countdown'),
                                    $this->html->span(')')
                                )
                                ->small()->primary()
                                ->jxnClick($rqHome->refresh(false))
                            )
                            ->setStyle('float:left;')
                            ->setClass('jaxon-supervisor-refresh-btn')
                        )
                    )
                    ->style('default')
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
                    //->setClass('col-sm-12')
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
                ['style' => 'margin-top:10px;'],
                $this->html->panelHeader(
                    $this->html->row(
                        $this->html->col(
                            $this->html->span()
                                ->addHtml($serverName . '&nbsp;' . $serverVersion)
                        )
                        ->width(5),
                        $this->html->col(
                            $this->html->div(
                                ['style' => 'float:right; padding-left:5px;'],
                                $this->html->button(
                                    $this->html->i(['class' => 'fa fa-stop']),
                                    $this->html->span()->addHtml('&nbsp;Stop all')
                                )
                                ->small()->danger()
                                ->jxnClick($rqServer->stop($server))
                            )
                            /*->pull('right')*/,
                            $this->html->div(
                                ['style' => 'float:right; padding-left:5px;'],
                                $this->html->button(
                                    $this->html->i(['class' => 'fa fa-play']),
                                    $this->html->span()->addHtml('&nbsp;Start all')
                                )
                                ->small()->success()
                                ->jxnClick($rqServer->start($server))
                            )
                            /*->pull('right')*/,
                            $this->html->div(
                                ['style' => 'float:right; padding-left:5px;'],
                                $this->html->button(
                                    $this->html->i(['class' => 'fa fa-redo']),
                                    $this->html->span()->addHtml('&nbsp;Restart all')
                                )
                                ->small()->primary()
                                ->jxnClick($rqServer->restart($server))
                            )
                            /*->pull('right')*/
                        )
                        ->width(7)
                    )
                    ->setStyle('width:100%;')
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
            ->style('default')
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
                $this->html->col()
                    ->width(5)
                    ->addText((string)$process['id']),
                $this->html->col(
                    ['style' => 'text-align:center;'],
                    $this->html->h5(
                        ['style' => 'margin:0;'],
                        $this->html->span(['class' => $labelClass])
                            ->addText((string)$process['statename'])
                    )
                )
                ->width(2),
                $this->html->col(['style' => 'text-align:center;'],)
                    ->width(3)
                    ->addText((string)$process['uptime']),
                $this->html->col(
                    $this->html->when($process->isRunning(), fn() =>
                        $this->html->list(
                            $this->html->div(
                                ['style' => 'float:right; padding-left:5px;'],
                                $this->html->button(
                                    ['class' => 'btn-stop'],
                                    $this->html->i(['class' => 'fa fa-stop'])
                                )
                                ->small()->danger()
                                ->jxnClick($rqProcess->stop($server, $processId))
                            )
                            /*->pull('right')*/,
                            $this->html->div(
                                ['style' => 'float:right; padding-left:5px;'],
                                $this->html->button(
                                    ['class' => 'btn-restart'],
                                    $this->html->i(['class' => 'fa fa-redo'])
                                )
                                ->small()->primary()
                                ->jxnClick($rqProcess->restart($server, $processId))
                            )
                            /*->pull('right')*/
                        )
                    ),
                    $this->html->when(!$process->isRunning(), fn() =>
                        $this->html->div(
                            ['style' => 'float:right; padding-left:5px;'],
                            $this->html->button(
                                ['class' => 'btn-start'],
                                $this->html->i(['class' => 'fa fa-play'])
                            )
                            ->small()->success()
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
