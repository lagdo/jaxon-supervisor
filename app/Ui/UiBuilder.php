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
     * @param BuilderInterface $ui
     */
    public function __construct(protected BuilderInterface $ui)
    {}

    /**
     * @return string
     */
    public function wrapper(): string
    {
        $rqHome = rq(Home::class);
        $joSupervisor = jo('jaxon.supervisor');
        return $this->ui->build(
            $this->ui->row(
                $this->ui->col(
                    $this->ui->panel(
                        $this->ui->panelBody(
                            $this->ui->div(
                                $this->ui->button(
                                    $this->ui->i(['class' => 'fa fa-play'])
                                )->small()
                                    ->success()
                                    ->jxnClick($joSupervisor->enableRefresh())
                            ) ->setStyle('float:left;')
                                ->setClass('jaxon-supervisor-refresh-btn jaxon-supervisor-refresh-disabled'),
                            $this->ui->div(
                                $this->ui->button(
                                    $this->ui->i(['class' => 'fa fa-stop'])
                                )->small()
                                    ->danger()
                                    ->jxnClick($joSupervisor->disableRefresh())
                            )->setStyle('float:left;')
                                ->setClass('jaxon-supervisor-refresh-btn jaxon-supervisor-refresh-enabled'),
                            $this->ui->div(
                                $this->ui->button(
                                    $this->ui->i(['class' => 'fa fa-sync']),
                                    $this->ui->span($this->ui->html('&nbsp;Refresh (')),
                                    $this->ui->span('0')
                                        ->setId('jaxon-supervisor-refresh-countdown'),
                                    $this->ui->span(')')
                                )->small()
                                    ->primary()
                                    ->jxnClick($rqHome->refresh(false))
                            )->setStyle('float:left;')
                                ->setClass('jaxon-supervisor-refresh-btn')
                        )
                    )->look('default')
                )->width(12)
            ),
            $this->ui->row($this->ui->jxnHtml($rqHome))->jxnBind($rqHome)
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
        return $this->ui->build(
            $this->ui->each($serverItemIds, fn($serverItemId) =>
                $this->ui->col()
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
        return $this->ui->build(
            $this->ui->panel(
                ['style' => 'margin-top:10px;'],
                $this->ui->panelHeader(
                    $this->ui->row(
                        $this->ui->col(
                            $this->ui->span($this->ui->html($serverName . '&nbsp;' . $serverVersion))
                        )->width(5),
                        $this->ui->col(
                            $this->ui->div(
                                ['style' => 'float:right; padding-left:5px;'],
                                $this->ui->button(
                                    $this->ui->i(['class' => 'fa fa-stop']),
                                    $this->ui->span($this->ui->html('&nbsp;Stop all'))
                                )->small()
                                    ->danger()
                                    ->jxnClick($rqServer->stop($server))
                            )/*->pull('right')*/,
                            $this->ui->div(
                                ['style' => 'float:right; padding-left:5px;'],
                                $this->ui->button(
                                    $this->ui->i(['class' => 'fa fa-play']),
                                    $this->ui->span($this->ui->html('&nbsp;Start all'))
                                )->small()
                                    ->success()
                                    ->jxnClick($rqServer->start($server))
                            )/*->pull('right')*/,
                            $this->ui->div(
                                ['style' => 'float:right; padding-left:5px;'],
                                $this->ui->button(
                                    $this->ui->i(['class' => 'fa fa-redo']),
                                    $this->ui->span($this->ui->html('&nbsp;Restart all'))
                                )->small()
                                    ->primary()
                                    ->jxnClick($rqServer->restart($server))
                            )/*->pull('right')*/
                        )->width(7)
                    )->setStyle('width:100%;')
                ),
                $this->ui->panelBody(
                    ['style' => 'padding:5px 15px;'],
                    $this->ui->each($processes, function($process) use($rqProcess, $clProcess) {
                        $clProcess->setProcess($process);
                        return $this->ui->div(
                            ['style' => 'margin:5px 0;'],
                            $this->ui->html($clProcess->html())
                        )->jxnBind($rqProcess, $clProcess->getItemId());
                    })
                )
            )->look('default')
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
        return $this->ui->build(
            $this->ui->row(
                $this->ui->col(
                    $this->ui->text((string)$process['id'])
                )->width(5),
                $this->ui->col(
                    ['style' => 'text-align:center;'],
                    $this->ui->h5(
                        ['style' => 'margin:0;'],
                        $this->ui->span(
                            ['class' => $labelClass],
                            $this->ui->text((string)$process['statename'])
                        )
                    )
                )->width(2),
                $this->ui->col(
                    ['style' => 'text-align:center;'],
                    $this->ui->text((string)$process['uptime'])
                )->width(3),
                $this->ui->col(
                    $this->ui->when($process->isRunning(), fn() =>
                        $this->ui->list(
                            $this->ui->div(
                                ['style' => 'float:right; padding-left:5px;'],
                                $this->ui->button(
                                    ['class' => 'btn-stop'],
                                    $this->ui->i(['class' => 'fa fa-stop'])
                                )->small()
                                    ->danger()
                                    ->jxnClick($rqProcess->stop($server, $processId))
                            )/*->pull('right')*/,
                            $this->ui->div(
                                ['style' => 'float:right; padding-left:5px;'],
                                $this->ui->button(
                                    ['class' => 'btn-restart'],
                                    $this->ui->i(['class' => 'fa fa-redo'])
                                )->small()
                                    ->primary()
                                    ->jxnClick($rqProcess->restart($server, $processId))
                            )/*->pull('right')*/
                        )
                    ),
                    $this->ui->when(!$process->isRunning(), fn() =>
                        $this->ui->div(
                            ['style' => 'float:right; padding-left:5px;'],
                            $this->ui->button(
                                ['class' => 'btn-start'],
                                $this->ui->i(['class' => 'fa fa-play'])
                            )->small()
                                ->success()
                                ->jxnClick($rqProcess->start($server, $processId))
                        )/*->pull('right')*/
                    )
                )->width(2),
            )
        );
    }
}
