<?php

namespace App\Jobs;

use Throwable;
use Spatie\Async\Pool;
use App\Models\Perangkat;
use App\Models\Log_status;
use Illuminate\Bus\Queueable;
use App\Models\detailperangkat;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PingPerangkatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $perangkatList = Perangkat::all();
        $detailList = detailperangkat::all();

        $pool = Pool::create();

        foreach ($perangkatList as $item) {
            $pool->add(function () use ($item) {
                $oldStatus = $item->status;
                $isAlive = $this->ping($item->ip_address); 

                $item->status = $isAlive ? '1' : '0';
                $item->mac_address = $isAlive ? $this->getMacAddress($item->ip_address) : null;
                $item->save();

                if ($oldStatus !== $item->status) {
                    $statusText = $item->status === '1' ? 'Online' : 'Offline';
                    $message = "Internet {$item->hostname} ({$item->ip_address}) {$statusText}.";
                    Log_status::create(['message' => $message]);
                    Log::info($message);
                }
            })->catch(function (Throwable $exception) use ($item) {
                Log::error("Ping gagal untuk {$item->hostname}: " . $exception->getMessage());
            });
        }

   
        foreach ($detailList as $detail) {
            $pool->add(function () use ($detail) {
                $oldStatus = $detail->status;
                $isAlive = $this->ping($detail->ip_address); 

                $detail->status = $isAlive ? '1' : '0';
                $detail->mac_address = $isAlive ? $this->getMacAddress($detail->ip_address) : null;
                $detail->save();

                if ($oldStatus !== $detail->status) {
                    $statusText = $detail->status === '1' ? 'Online' : 'Offline';
                    $message = "Perangkat {$detail->namaperangkat} | {$detail->internet->hostname} ({$detail->ip_address}) {$statusText}.";
                    Log_status::create(['message' => $message]);
                    Log::info($message);
                }
            })->catch(function (Throwable $exception) use ($detail) {
                Log::error("Ping gagal untuk Detail Perangkat {$detail->namaperangkat}: " . $exception->getMessage());
            });
        }

        $pool->wait();
    }

    private function ping($ip)
    {
        $output = [];
        $result = null;

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec("ping -n 1 -w 1000 {$ip}", $output, $result);
        } else {
            exec("ping -c 1 -W 1 {$ip}", $output, $result);
        }

        return $result === 0 && !preg_grep('/unreachable/i', $output);
    }

    private function getMacAddress($ip)
    {
        $output = [];

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec("arp -a {$ip}", $output);
            foreach ($output as $line) {
                if (strpos($line, $ip) !== false) {
                    $parts = preg_split('/\s+/', trim($line));
                    return $parts[1] ?? null;
                }
            }
        } else {
            exec("arp -n {$ip}", $output);
            foreach ($output as $line) {
                if (strpos($line, $ip) !== false) {
                    $parts = preg_split('/\s+/', trim($line));
                    return $parts[2] ?? null;
                }
            }
        }

        return null;
    }
}
