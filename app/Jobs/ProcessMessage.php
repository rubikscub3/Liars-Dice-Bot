<?php

namespace App\Jobs;

use App\Http\Controllers\LiarsDiceBotController;
use BotMan\BotMan\BotMan;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bot;
    protected $liars_dice;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(BotMan $bot)
    {
        $this->bot = $bot;
        $this->liars_dice = new LiarsDiceBotController;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $msg_txt = $this->bot->getMessage()->getText();

        if(preg_match('/^start game.*$/', strtolower($msg_txt))) {
            $this->liars_dice->start($this->bot);
        } elseif(preg_match('/^([1-9][0-9]{0,3}(,|\.)[1-6])$/', $msg_txt)) {
            $this->liars_dice->playRound($this->bot);
        } elseif(preg_match('/^play liar.*$/', strtolower($msg_txt))) {
            $this->liars_dice->host($this->bot);
        } elseif(preg_match('/^liar.*$/', strtolower($msg_txt))) {
            $this->liars_dice->playRound($this->bot);
        } elseif(preg_match('/^abort game.*$/', strtolower($msg_txt))) {
            $this->liars_dice->abort($this->bot);
        } elseif(preg_match('/^say .*$/i', $msg_txt)) {
            $this->liars_dice->say($this->bot);
        }
    }
}
