<?php

namespace App\Console\Commands;// cụ thể là Scheduled Commands tức là cứ sau bao nhiêu phút là chạy

use App\Models\Seat;
use App\Events\SeatStatusChanged;
use Illuminate\Console\Command;

class ReleaseExpiredSeatHolds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seats:release-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release seats that have expired holds';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   //STATUS_HELD là cách viết để cho dễ nhớ tránh sai định dạng của enum
        $expiredSeats = Seat::where('status', Seat::STATUS_HELD)
            ->where('reserved_until', '<', now())
            ->get();

        $count = 0;

        foreach ($expiredSeats as $seat) {
            $seat->status = Seat::STATUS_AVAILABLE;
            $seat->held_by_user_id = null;
            $seat->reserved_until = null;
            $seat->save();

            // Broadcast để các client khác biết
            broadcast(new SeatStatusChanged($seat))->toOthers();

            $count++;
        }

        $this->info("Released {$count} expired seat holds.");

        return Command::SUCCESS;
    }
}
